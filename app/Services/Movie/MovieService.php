<?php

namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieService
{
    /**
     * Get all movies with filters
     */
    public function getAllMovies(array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['release_date_from'])) {
            $query->where('release_date', '>=', $filters['release_date_from']);
        }

        if (isset($filters['release_date_to'])) {
            $query->where('release_date', '<=', $filters['release_date_to']);
        }

        return $query->orderBy('release_date', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get movie by ID
     */
    public function getMovieById(int $id): ?Movie
    {
        return Movie::with(['poster', 'trailer', 'showtimes', 'reviews'])->find($id);
    }

    /**
     * Create a new movie
     */
    public function createMovie(array $data): Movie
    {
        return Movie::create($data);
    }

    /**
     * Update movie
     */
    public function updateMovie(int $id, array $data): bool
    {
        $movie = Movie::find($id);
        
        if (!$movie) {
            return false;
        }

        return $movie->update($data);
    }

    /**
     * Delete movie (soft delete)
     */
    public function deleteMovie(int $id): bool
    {
        $movie = Movie::find($id);
        
        if (!$movie) {
            return false;
        }

        return $movie->delete();
    }
}


