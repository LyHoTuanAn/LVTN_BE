<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Voucher;
use App\Services\Booking\BookingValidationService;
use App\Services\Payment\StripeService;
use App\Services\Voucher\VoucherValidationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected BookingValidationService $validationService,
        protected VoucherValidationService $voucherValidationService,
        protected StripeService $stripeService
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

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
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
     * Create a new booking with Stripe payment intent
     *
     * @return array{booking: Booking, payment: array}
     */
    public function createBooking(array $data, int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            // Validate seats availability
            $this->validationService->validateSeatsAvailable($data['showtime_id'], $data['seat_ids']);

            $showtime = Showtime::with('movie')->findOrFail($data['showtime_id']);
            
            // Calculate price
            $seatCount = count($data['seat_ids']);
            $price = $showtime->price * $seatCount;
            $voucherAmount = 0;
            $totalPrice = $price;

            // Apply voucher if provided
            if (isset($data['voucher_code']) && $data['voucher_code']) {
                $voucher = Voucher::where('code', $data['voucher_code'])->first();
                
                if (!$voucher) {
                    throw new \Exception(__('errors.VOUCHER_NOT_FOUND'));
                }
                
                // Validate voucher using VoucherValidationService
                $this->voucherValidationService->validateVoucherForUser(
                    $voucher,
                    $userId,
                    $showtime->movie_id
                );
                
                // Calculate discount amount
                $voucherAmount = $this->voucherValidationService->calculateDiscountAmount(
                    $voucher,
                    $price
                );
                
                $totalPrice = max(0, $price - $voucherAmount);
                $data['voucher_id'] = $voucher->id;
                $data['voucher_amount'] = $voucherAmount;
                
                // Update voucher used count
                $voucher->increment('used_count');
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
                'payment_method' => 'stripe',
            ]);

            // Attach seats
            $booking->seats()->attach($data['seat_ids']);

            // Load relationships
            $booking->load(['user', 'showtime.movie', 'seats', 'voucher']);

            // Create Stripe Checkout Session (returns direct payment URL)
            $checkoutSession = $this->stripeService->createCheckoutSession($booking, [
                'movie_title' => $showtime->movie->title ?? 'Movie Ticket',
            ]);

            // Update booking with checkout session ID
            $booking->update([
                'payment_intent_id' => $checkoutSession->id, // Store session ID for webhook tracking
            ]);

            return [
                'booking' => $booking,
                'payment' => [
                    'checkout_url' => $checkoutSession->url, // Direct payment URL - just open this!
                    'expires_at' => date('Y-m-d H:i:s', $checkoutSession->expires_at),
                ],
            ];
        });
    }

    /**
     * Calculate ticket price for preview (before creating booking)
     */
    public function calculateTicketPrice(array $data, int $userId): array
    {
        // Validate seats availability
        $this->validationService->validateSeatsAvailable($data['showtime_id'], $data['seat_ids']);

        // Load showtime with movie
        $showtime = Showtime::with(['movie'])->findOrFail($data['showtime_id']);

        // Check if showtime is available
        if (!$this->validationService->isShowtimeAvailable($data['showtime_id'])) {
            throw new \Exception(__('errors.SHOWTIME_INVALID_STATUS'));
        }

        // Load seats
        $seats = Seat::whereIn('id', $data['seat_ids'])
            ->orderBy('row')
            ->orderBy('number')
            ->get();

        // Calculate price
        $seatCount = count($data['seat_ids']);
        $price = (float) $showtime->price * $seatCount;
        $voucherAmount = 0;
        $totalPrice = $price;
        $voucherCode = null;

        // Apply voucher if provided (validate but don't update usage count)
        if (isset($data['voucher_code']) && $data['voucher_code']) {
            $voucher = Voucher::where('code', $data['voucher_code'])->first();

            if (!$voucher) {
                throw new \Exception(__('errors.VOUCHER_NOT_FOUND'));
            }

            // Validate voucher using VoucherValidationService (but don't increment usage)
            $this->voucherValidationService->validateVoucherForUser(
                $voucher,
                $userId,
                $showtime->movie_id
            );

            // Calculate discount amount
            $voucherAmount = $this->voucherValidationService->calculateDiscountAmount(
                $voucher,
                $price
            );

            $totalPrice = max(0, $price - $voucherAmount);
            $voucherCode = $voucher->code;
        }

        return [
            'showtime' => $showtime,
            'seats' => $seats,
            'seat_count' => $seatCount,
            'price' => $price,
            'voucher_code' => $voucherCode,
            'voucher_discount' => $voucherAmount,
            'total_price' => $totalPrice,
        ];
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


