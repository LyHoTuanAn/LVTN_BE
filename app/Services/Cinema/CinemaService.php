<?php

namespace App\Services\Cinema;

use App\Models\Cinema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CinemaService
{
    /**
     * Get all cinemas with filters
     */
    public function getAllCinemas(array $filters = []): LengthAwarePaginator
    {
        $query = Cinema::query()->with(['user', 'rooms']);

        if (isset($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get cinema by ID
     */
    public function getCinemaById(int $id): ?Cinema
    {
        return Cinema::with(['user', 'rooms.seats'])->find($id);
    }

    /**
     * Create a new cinema
     */
    public function createCinema(array $data): Cinema
    {
        return Cinema::create($data);
    }

    /**
     * Update cinema
     */
    public function updateCinema(int $id, array $data): bool
    {
        $cinema = Cinema::find($id);
        
        if (!$cinema) {
            return false;
        }

        return $cinema->update($data);
    }

    /**
     * Delete cinema (soft delete)
     */
    public function deleteCinema(int $id): bool
    {
        $cinema = Cinema::find($id);
        
        if (!$cinema) {
            return false;
        }

        return $cinema->delete();
    }
}


