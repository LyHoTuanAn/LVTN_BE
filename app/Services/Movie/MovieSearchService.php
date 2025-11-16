<?php

namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieSearchService
{
    /**
     * Search movies by title, description
     */
    public function search(string $keyword, array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer']);

        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%');
        });

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
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
     * Get movies by status
     */
    public function getMoviesByStatus(string $status, int $limit = 10): Collection
    {
        return Movie::where('status', $status)
            ->with(['poster'])
            ->orderBy('release_date', 'desc')
            ->limit($limit)
            ->get();
    }
}


