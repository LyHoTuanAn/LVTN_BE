<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customers (users with role customer)
        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->get();

        // If no customers, use all users (fallback)
        if ($customers->isEmpty()) {
            $customers = User::all();
            if ($customers->isEmpty()) {
                $this->command->warn('No users found. Please seed users first.');
                return;
            }
            $this->command->info('No customers found. Using all users instead.');
        }

        // Get showtimes
        $showtimes = Showtime::with(['room.seats', 'movie'])->get();

        if ($showtimes->isEmpty()) {
            $this->command->warn('No showtimes found. Please seed showtimes first.');
            return;
        }

        // Get vouchers (optional)
        $vouchers = Voucher::where('status', 'active')->get();

        $statuses = ['pending', 'confirmed', 'completed', 'canceled'];
        $paymentMethods = ['cash', 'bank_transfer', 'credit_card', 'momo', 'vnpay'];
        
        $bookingCount = 0;
        $maxBookings = 10; // Số lượng bookings muốn tạo

        foreach ($showtimes as $showtime) {
            if ($bookingCount >= $maxBookings) {
                break;
            }

            // Get seats for this showtime's room
            $allSeats = $showtime->room->seats;
            
            if ($allSeats->isEmpty()) {
                $this->command->warn("Showtime #{$showtime->id} - Room '{$showtime->room->name}' has no seats. Skipping...");
                continue;
            }

            // Get already booked seats for this showtime from database (ONLY paid bookings reserve seats)
            $existingBookedSeatIds = Booking::where('showtime_id', $showtime->id)
                ->where('status', '!=', 'canceled')
                ->where('is_paid', true) // Only paid bookings reserve seats
                ->get()
                ->pluck('seats')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->toArray();

            // Also exclude maintenance/disabled seats
            $maintenanceSeatIds = $showtime->room->seats()
                ->whereIn('status', ['maintenance', 'disabled'])
                ->pluck('id')
                ->toArray();
            
            $existingBookedSeatIds = array_merge($existingBookedSeatIds, $maintenanceSeatIds);

            // Get available seats (not booked in database)
            $availableSeats = $allSeats->whereNotIn('id', $existingBookedSeatIds);
            
            if ($availableSeats->isEmpty()) {
                continue; // All seats already booked
            }

            // Random number of bookings for this showtime (1-5 bookings)
            $bookingsForShowtime = rand(1, min(5, floor($availableSeats->count() / 2)));

            // Track booked seats for this showtime in this seeder run to avoid double booking
            $bookedSeatIds = $existingBookedSeatIds;

            for ($i = 0; $i < $bookingsForShowtime; $i++) {
                if ($bookingCount >= $maxBookings) {
                    break;
                }

                // Get available seats (not yet booked in this seeder run)
                $remainingSeats = $availableSeats->whereNotIn('id', $bookedSeatIds);
                
                if ($remainingSeats->isEmpty()) {
                    break; // No more seats available for this showtime
                }

                // Random customer
                $customer = $customers->random();

                // Random number of seats (1-4 seats per booking)
                $seatCount = rand(1, min(4, $remainingSeats->count()));
                $selectedSeats = $remainingSeats->random($seatCount);

                // Calculate price
                $price = $showtime->price * $seatCount;
                $voucherAmount = 0;
                $voucherId = null;
                $totalPrice = $price;

                // Randomly apply voucher (30% chance)
                if ($vouchers->isNotEmpty() && rand(1, 100) <= 30) {
                    $voucher = $vouchers->random();
                    
                    if ($voucher->isValid()) {
                        $voucherId = $voucher->id;
                        
                        if ($voucher->type === 'percentage') {
                            $voucherAmount = $price * ($voucher->amount / 100);
                        } else {
                            $voucherAmount = min($voucher->amount, $price);
                        }
                        
                        $totalPrice = max(0, $price - $voucherAmount);
                    }
                }

                // Random status (weighted: more confirmed/completed than pending/canceled)
                $statusWeights = [
                    'pending' => 20,
                    'confirmed' => 30,
                    'completed' => 40,
                    'canceled' => 10,
                ];
                $status = $this->weightedRandom($statusWeights);

                // Payment status: if confirmed/completed, more likely to be paid
                // IMPORTANT: Only paid bookings reserve seats. Unpaid bookings don't block seats.
                $isPaid = false;
                $paymentMethod = null;
                
                if (in_array($status, ['confirmed', 'completed'])) {
                    $isPaid = rand(1, 100) <= 80; // 80% chance of being paid
                    if ($isPaid) {
                        $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    }
                } elseif ($status === 'pending') {
                    $isPaid = rand(1, 100) <= 30; // 30% chance of being paid
                    if ($isPaid) {
                        $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    }
                }

                // Generate unique booking code
                do {
                    $code = strtoupper(Str::random(8));
                } while (Booking::where('code', $code)->exists());

                // Create booking
                $booking = Booking::create([
                    'user_id' => $customer->id,
                    'showtime_id' => $showtime->id,
                    'code' => $code,
                    'price' => $price,
                    'total_price' => $totalPrice,
                    'voucher_id' => $voucherId,
                    'voucher_amount' => $voucherAmount,
                    'status' => $status,
                    'is_paid' => $isPaid,
                    'payment_method' => $paymentMethod,
                    'created_at' => now()->subDays(rand(0, 30)), // Random date within last 30 days
                ]);

                // Only attach seats if booking is paid (unpaid bookings don't reserve seats)
                // Unpaid bookings are still created but don't block seats - seats remain available
                if ($isPaid) {
                    $booking->seats()->attach($selectedSeats->pluck('id'));
                    
                    // Track booked seats (only paid bookings reserve seats)
                    $bookedSeatIds = array_merge($bookedSeatIds, $selectedSeats->pluck('id')->toArray());
                }

                $bookingCount++;
            }
        }

        if ($bookingCount > 0) {
            $this->command->info("Created {$bookingCount} bookings successfully!");
        } else {
            $this->command->warn("No bookings created. Please check:");
            $this->command->warn("- Ensure there are users in the database");
            $this->command->warn("- Ensure there are showtimes with rooms that have seats");
            $this->command->warn("- Run: php artisan db:seed --class=RoomSeeder (if exists) to create seats");
        }
    }

    /**
     * Weighted random selection
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $key => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $key;
            }
        }

        return array_key_first($weights);
    }
}
