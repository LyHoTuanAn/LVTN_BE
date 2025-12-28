<?php

namespace App\Services\Cinema;

use App\Models\Room;
use App\Models\Seat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoomService
{
    /**
     * Get all rooms with filters
     */
    public function getAllRooms(array $filters = []): LengthAwarePaginator
    {
        $query = Room::query()->with(['cinema', 'seats']);

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['cinema_id'])) {
            $query->where('cinema_id', $filters['cinema_id']);
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

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
     * Create a new room with seats
     */
    public function createRoom(array $data): Room
    {
        return DB::transaction(function () use ($data) {
            // Create the room
            $room = Room::create($data);

            // Auto-generate seats based on seat_count
            $seatCount = $data['seat_count'] ?? 0;
            if ($seatCount > 0) {
                $this->generateSeatsForRoom($room->id, $seatCount);
            }

            return $room;
        });
    }

    /**
     * Generate seats for a room
     * Layout: 10 seats per row (A1-A10, B1-B10, ...)
     */
    protected function generateSeatsForRoom(int $roomId, int $seatCount): void
    {
        $seatsPerRow = 10;
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        
        $seatNumber = 0;
        $rowIndex = 0;

        while ($seatNumber < $seatCount && $rowIndex < count($rows)) {
            $row = $rows[$rowIndex];
            $seatsInThisRow = min($seatsPerRow, $seatCount - $seatNumber);

            for ($number = 1; $number <= $seatsInThisRow; $number++) {
                Seat::create([
                    'room_id' => $roomId,
                    'row' => $row,
                    'number' => $number,
                    'type' => 'normal',
                    'status' => 'active',
                ]);
                $seatNumber++;
            }

            $rowIndex++;
        }
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


