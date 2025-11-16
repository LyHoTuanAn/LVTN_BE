<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingValidationService
{
    /**
     * Validate if seats are available for booking
     */
    public function validateSeatsAvailable(int $showtimeId, array $seatIds): void
    {
        $showtime = Showtime::findOrFail($showtimeId);
        $roomId = $showtime->room_id;

        // Check if seats exist and belong to the room
        $seats = Seat::whereIn('id', $seatIds)
            ->where('room_id', $roomId)
            ->where('status', 'active')
            ->get();

        if ($seats->count() !== count($seatIds)) {
            throw new \Exception('Some seats are invalid or inactive');
        }

        // Check if seats are already booked
        $bookedSeats = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->whereIn('booking_seats.seat_id', $seatIds)
            ->where('bookings.status', '!=', 'canceled')
            ->pluck('booking_seats.seat_id')
            ->toArray();

        if (!empty($bookedSeats)) {
            throw new \Exception('Some seats are already booked');
        }
    }

    /**
     * Check if showtime is available for booking
     */
    public function isShowtimeAvailable(int $showtimeId): bool
    {
        $showtime = Showtime::find($showtimeId);

        if (!$showtime) {
            return false;
        }

        return in_array($showtime->status, ['scheduled', 'ongoing']);
    }

    /**
     * Get available seats for a showtime
     */
    public function getAvailableSeats(int $showtimeId): array
    {
        $showtime = Showtime::with('room.seats')->findOrFail($showtimeId);
        $room = $showtime->room;
        
        $allSeats = $room->seats()->where('status', 'active')->pluck('id')->toArray();

        $bookedSeats = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->where('bookings.status', '!=', 'canceled')
            ->pluck('booking_seats.seat_id')
            ->toArray();

        return array_diff($allSeats, $bookedSeats);
    }
}

