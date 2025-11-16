<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Showtime;
use App\Models\Voucher;
use App\Services\Booking\BookingValidationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected BookingValidationService $validationService
    ) {
    }

    /**
     * Get all bookings with filters
     */
    public function getAllBookings(array $filters = []): LengthAwarePaginator
    {
        $query = Booking::query()->with(['user', 'showtime.movie', 'showtime.room', 'seats', 'voucher']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['showtime_id'])) {
            $query->where('showtime_id', $filters['showtime_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_paid'])) {
            $query->where('is_paid', $filters['is_paid']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get booking by ID
     */
    public function getBookingById(int $id): ?Booking
    {
        return Booking::with(['user', 'showtime.movie', 'showtime.room', 'seats', 'voucher'])->find($id);
    }

    /**
     * Get booking by code
     */
    public function getBookingByCode(string $code): ?Booking
    {
        return Booking::with(['user', 'showtime.movie', 'showtime.room', 'seats', 'voucher'])
            ->where('code', $code)
            ->first();
    }

    /**
     * Create a new booking
     */
    public function createBooking(array $data, int $userId): Booking
    {
        return DB::transaction(function () use ($data, $userId) {
            // Validate seats availability
            $this->validationService->validateSeatsAvailable($data['showtime_id'], $data['seat_ids']);

            $showtime = Showtime::findOrFail($data['showtime_id']);
            
            // Calculate price
            $seatCount = count($data['seat_ids']);
            $price = $showtime->price * $seatCount;
            $voucherAmount = 0;
            $totalPrice = $price;

            // Apply voucher if provided
            if (isset($data['voucher_code']) && $data['voucher_code']) {
                $voucher = Voucher::where('code', $data['voucher_code'])->first();
                
                if ($voucher && $voucher->isValid()) {
                    if ($voucher->type === 'percentage') {
                        $voucherAmount = $price * ($voucher->amount / 100);
                    } else {
                        $voucherAmount = $voucher->amount;
                    }
                    
                    $totalPrice = max(0, $price - $voucherAmount);
                    $data['voucher_id'] = $voucher->id;
                    $data['voucher_amount'] = $voucherAmount;
                    
                    // Update voucher used count
                    $voucher->increment('used_count');
                }
            }

            // Generate booking code
            $code = $this->generateBookingCode();

            // Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'showtime_id' => $data['showtime_id'],
                'code' => $code,
                'price' => $price,
                'total_price' => $totalPrice,
                'voucher_id' => $data['voucher_id'] ?? null,
                'voucher_amount' => $voucherAmount,
                'status' => 'pending',
                'is_paid' => false,
            ]);

            // Attach seats
            $booking->seats()->attach($data['seat_ids']);

            return $booking->load(['user', 'showtime', 'seats', 'voucher']);
        });
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(int $id, int $userId): bool
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return false;
        }

        // Check if booking can be cancelled (within 30 minutes)
        if (now()->diffInMinutes($booking->created_at) > 30) {
            return false;
        }

        $booking->status = 'canceled';
        return $booking->save();
    }

    /**
     * Generate unique booking code
     */
    protected function generateBookingCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Booking::where('code', $code)->exists());

        return $code;
    }
}


