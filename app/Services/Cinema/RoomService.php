<?php

namespace App\Services\Cinema;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

class RoomService
{
    /**
     * Get all rooms for a cinema
     */
    public function getRoomsByCinema(int $cinemaId): Collection
    {
        return Room::where('cinema_id', $cinemaId)
            ->with(['seats'])
            ->get();
    }

    /**
     * Get room by ID
     */
    public function getRoomById(int $id): ?Room
    {
        return Room::with(['cinema', 'seats'])->find($id);
    }

    /**
     * Create a new room
     */
    public function createRoom(array $data): Room
    {
        return Room::create($data);
    }

    /**
     * Update room
     */
    public function updateRoom(int $id, array $data): bool
    {
        $room = Room::find($id);
        
        if (!$room) {
            return false;
        }

        return $room->update($data);
    }

    /**
     * Delete room (soft delete)
     */
    public function deleteRoom(int $id): bool
    {
        $room = Room::find($id);
        
        if (!$room) {
            return false;
        }

        return $room->delete();
    }
}


