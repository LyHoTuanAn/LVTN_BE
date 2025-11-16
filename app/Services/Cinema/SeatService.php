<?php

namespace App\Services\Cinema;

use App\Models\Seat;
use Illuminate\Database\Eloquent\Collection;

class SeatService
{
    /**
     * Get all seats for a room
     */
    public function getSeatsByRoom(int $roomId): Collection
    {
        return Seat::where('room_id', $roomId)->get();
    }

    /**
     * Get seat by ID
     */
    public function getSeatById(int $id): ?Seat
    {
        return Seat::with(['room'])->find($id);
    }

    /**
     * Create seats for a room (bulk)
     */
    public function createSeatsForRoom(int $roomId, array $seatsData): void
    {
        foreach ($seatsData as $seatData) {
            $seatData['room_id'] = $roomId;
            Seat::create($seatData);
        }
    }

    /**
     * Update seat
     */
    public function updateSeat(int $id, array $data): bool
    {
        $seat = Seat::find($id);
        
        if (!$seat) {
            return false;
        }

        return $seat->update($data);
    }

    /**
     * Delete seat
     */
    public function deleteSeat(int $id): bool
    {
        $seat = Seat::find($id);
        
        if (!$seat) {
            return false;
        }

        return $seat->delete();
    }
}


