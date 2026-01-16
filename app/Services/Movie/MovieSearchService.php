<?php

namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieSearchService
{
    /**
     * Search movies by title, description
     * 
     * Note: Status filter uses computed status (based on release_date) instead of DB status
     */
    public function search(string $keyword, array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer']);

        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', '%' . $keyword . '%')
              ->orWhere('description', 'like', '%' . $keyword . '%');
        });

        if (isset($filters['release_date_from'])) {
            $query->where('release_date', '>=', $filters['release_date_from']);
        }

        if (isset($filters['release_date_to'])) {
            $query->where('release_date', '<=', $filters['release_date_to']);
        }

        $movies = $query->orderBy('release_date', 'desc')->get();

        // Filter by computed status if provided
        if (isset($filters['status'])) {
            $statusFilter = strtoupper($filters['status']);
            $movies = $movies->filter(function ($movie) use ($statusFilter) {
                return $movie->getComputedStatus() === $statusFilter;
            });
        }

        // Manual pagination
        $perPage = $filters['per_page'] ?? 15;
        $currentPage = request()->get('page', 1);
        $items = $movies->forPage($currentPage, $perPage);
        $total = $movies->count();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Get movies by computed status
     * 
     * Note: Uses computed status (based on release_date) instead of DB status
     * 
     * @param string $status COMING_SOON or NOW_SHOWING
     * @param int $limit
     * @return Collection
     */
    public function getMoviesByStatus(string $status, int $limit = 10): Collection
    {
        $statusFilter = strtoupper($status);
        
        return Movie::with(['poster'])
            ->get()
            ->filter(function ($movie) use ($statusFilter) {
                return $movie->getComputedStatus() === $statusFilter;
            })
            ->sortByDesc('release_date')
            ->take($limit)
            ->values();
    }
}


