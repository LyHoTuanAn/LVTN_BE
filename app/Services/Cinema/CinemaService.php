<?php

namespace App\Services\Cinema;

use App\Models\Cinema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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

    /**
     * Create multiple cinemas at once
     * 
     * @param array $cinemasData Array of cinema data arrays
     * @return Collection Collection of created Cinema models
     * @throws \Exception If any cinema creation fails
     */
    public function createManyCinemas(array $cinemasData): Collection
    {
        return DB::transaction(function () use ($cinemasData) {
            $createdCinemas = collect();

            foreach ($cinemasData as $cinemaData) {
                $cinema = Cinema::create($cinemaData);
                $createdCinemas->push($cinema);
            }

            return $createdCinemas;
        });
    }

    /**
     * Validate cinemas data before bulk creation
     * 
     * @param array $cinemasData Array of cinema data arrays
     * @return array Array of validation errors (empty if valid)
     */
    public function validateManyCinemas(array $cinemasData): array
    {
        $errors = [];
        $names = [];
        
        foreach ($cinemasData as $index => $cinemaData) {
            $cinemaErrors = [];
            
            // Check required fields
            if (empty($cinemaData['name'])) {
                $cinemaErrors['name'] = 'Name is required';
            } else {
                // Check for duplicate names within the batch
                if (in_array($cinemaData['name'], $names)) {
                    $cinemaErrors['name'] = 'Duplicate cinema name in batch';
                } else {
                    $names[] = $cinemaData['name'];
                    
                    // Check if name already exists in database
                    $existingCinema = Cinema::where('name', $cinemaData['name'])->first();
                    if ($existingCinema) {
                        $cinemaErrors['name'] = 'Cinema with this name already exists';
                    }
                }
            }
            
            if (empty($cinemaData['location'])) {
                $cinemaErrors['location'] = 'Location is required';
            }
            
            if (empty($cinemaData['address'])) {
                $cinemaErrors['address'] = 'Address is required';
            }
            
            if (!empty($cinemaErrors)) {
                $errors[$index] = $cinemaErrors;
            }
        }
        
        return $errors;
    }
}


