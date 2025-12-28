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

        // Check if all seats exist
        $allSeats = Seat::whereIn('id', $seatIds)->get();
        if ($allSeats->count() !== count($seatIds)) {
            $foundIds = $allSeats->pluck('id')->toArray();
            $missingIds = array_diff($seatIds, $foundIds);
            throw new \Exception(__('errors.SEAT_NOT_FOUND') . ' (IDs: ' . implode(', ', $missingIds) . ')');
        }

        // Check if seats belong to the room
        $seatsInRoom = $allSeats->where('room_id', $roomId);
        if ($seatsInRoom->count() !== count($seatIds)) {
            $invalidIds = $allSeats->where('room_id', '!=', $roomId)->pluck('id')->toArray();
            throw new \Exception(__('errors.SEAT_INVALID_ROOM') . ' (IDs: ' . implode(', ', $invalidIds) . ')');
        }

        // Check if seats are active
        $activeSeats = $allSeats->where('status', 'active');
        if ($activeSeats->count() !== count($seatIds)) {
            $inactiveIds = $allSeats->where('status', '!=', 'active')->pluck('id')->toArray();
            throw new \Exception(__('errors.SEAT_INACTIVE') . ' (IDs: ' . implode(', ', $inactiveIds) . ')');
        }

        // Check if seats are already booked (only paid bookings reserve seats)
        $bookedSeats = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.showtime_id', $showtimeId)
            ->whereIn('booking_seats.seat_id', $seatIds)
            ->where('bookings.status', '!=', 'canceled')
            ->where('bookings.is_paid', true)
            ->pluck('booking_seats.seat_id')
            ->toArray();

        if (!empty($bookedSeats)) {
            throw new \Exception(__('errors.SEAT_ALREADY_BOOKED') . ' (IDs: ' . implode(', ', $bookedSeats) . ')');
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
            ->where('bookings.is_paid', true) // Only paid bookings reserve seats
            ->pluck('booking_seats.seat_id')
            ->toArray();

        return array_diff($allSeats, $bookedSeats);
    }
}

