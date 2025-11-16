<?php

namespace App\Services\Showtime;

use App\Models\Showtime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeService
{
    /**
     * Get all showtimes with filters
     */
    public function getAllShowtimes(array $filters = []): LengthAwarePaginator
    {
        $query = Showtime::query()->with(['movie', 'room.cinema']);

        if (isset($filters['movie_id'])) {
            $query->where('movie_id', $filters['movie_id']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
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
     */
    public function getShowtimesByMovie(int $movieId, ?string $date = null): Collection
    {
        $query = Showtime::where('movie_id', $movieId)
            ->with(['room.cinema'])
            ->where('status', '!=', 'cancelled');

        if ($date) {
            $query->where('date', $date);
        }

        return $query->orderBy('date')->orderBy('start_time')->get();
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


