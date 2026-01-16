<?php

namespace App\Services\Showtime;

use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeService
{
    /**
     * Get all showtimes with filters
     */
    public function getAllShowtimes(array $filters = []): LengthAwarePaginator
    {
        $query = Showtime::query()->with(['movie', 'room.cinema', 'room.roomType']);

        if (isset($filters['movie_id'])) {
            $query->where('movie_id', $filters['movie_id']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['room_type_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            });
        }

        if (isset($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date')->orderBy('start_time')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get showtime by ID
     */
    public function getShowtimeById(int $id): ?Showtime
    {
        return Showtime::with(['movie', 'room.cinema', 'bookings'])->find($id);
    }

    /**
     * Get showtimes by movie
     * Filters out past showtimes (date < today OR date = today AND start_time < now)
     */
    public function getShowtimesByMovie(int $movieId, array $filters = []): Collection
    {
        $now = now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        $query = Showtime::where('movie_id', $movieId)
            ->with(['room.cinema']);

        if (isset($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (isset($filters['room_type_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            });
        }

        $showtimes = $query->orderBy('date')->orderBy('start_time')->get();

        // Filter out past showtimes
        $showtimes = $showtimes->filter(function ($showtime) use ($today, $currentTime) {
            $showtimeDate = $showtime->date->format('Y-m-d');
            
            // Remove showtimes from past dates
            if ($showtimeDate < $today) {
                return false;
            }
            
            // For today's showtimes, remove those that have already started
            if ($showtimeDate === $today) {
                $startTime = is_string($showtime->start_time) 
                    ? $showtime->start_time 
                    : $showtime->start_time->format('H:i:s');
                return $startTime > $currentTime;
            }
            
            // Future dates: keep all
            return true;
        });

        return $showtimes->values();
    }

    /**
     * Get seats with booking status for a showtime
     * 
     * @param int $showtimeId
     * @return array{showtime: Showtime|null, seats: Collection, booked_count: int, available_count: int, total_count: int}
     */
    public function getSeatsWithStatus(int $showtimeId): ?array
    {
        $showtime = Showtime::with(['movie', 'room.cinema'])->find($showtimeId);
        
        if (!$showtime) {
            return null;
        }

        // Get all seats of the room
        $seats = Seat::where('room_id', $showtime->room_id)
            ->orderBy('row')
            ->orderBy('number')
            ->get();

        // Get booked seat IDs for this showtime (only confirmed/pending bookings)
        $bookedSeatIds = Booking::where('showtime_id', $showtimeId)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->with('seats')
            ->get()
            ->pluck('seats')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // Add booking status to each seat
        $seats->each(function ($seat) use ($bookedSeatIds) {
            $seat->booking_status = in_array($seat->id, $bookedSeatIds) ? 'booked' : 'available';
        });

        // Group seats by row
        $seatsByRow = $seats->groupBy('row')->map(function ($rowSeats, $row) {
            return [
                'row' => $row,
                'seats' => $rowSeats->values(),
            ];
        })->values();

        return [
            'showtime' => $showtime,
            'seats' => $seats,
            'seats_by_row' => $seatsByRow,
            'booked_count' => count($bookedSeatIds),
            'available_count' => $seats->count() - count($bookedSeatIds),
            'total_count' => $seats->count(),
        ];
    }

    /**
     * Create a new showtime
     */
    public function createShowtime(array $data): Showtime
    {
        return Showtime::create($data);
    }

    /**
     * Update showtime
     */
    public function updateShowtime(int $id, array $data): bool
    {
        $showtime = Showtime::find($id);
        
        if (!$showtime) {
            return false;
        }

        return $showtime->update($data);
    }

    /**
     * Delete showtime (soft delete)
     */
    public function deleteShowtime(int $id): bool
    {
        $showtime = Showtime::find($id);
        
        if (!$showtime) {
            return false;
        }

        return $showtime->delete();
    }
}



