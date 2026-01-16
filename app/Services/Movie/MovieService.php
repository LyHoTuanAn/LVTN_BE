<?php

namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieService
{
    /**
     * Search movies with advanced filters
     * 
     * @param array $filters
     *   - keyword: Search in title and description
     *   - genre: Filter by genre (exact match)
     *   - status: Filter by computed status (COMING_SOON, NOW_SHOWING) - dựa trên release_date
     *   - age_classification: Filter by age classification (P, K, T13, T16, T18, C)
     *   - duration_min: Minimum duration in minutes
     *   - duration_max: Maximum duration in minutes  
     *   - release_year: Filter by release year
     *   - sort_by: Sort field (title, release_date, duration, created_at)
     *   - sort_order: Sort order (asc, desc)
     *   - per_page: Items per page (default 15)
     */
    public function searchMovies(array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer']);

        // Search by keyword (title and description)
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        // Filter by genre
        if (!empty($filters['genre'])) {
            $query->where('genre', $filters['genre']);
        }

        // Filter by age classification
        if (!empty($filters['age_classification'])) {
            $query->where('age_classification', $filters['age_classification']);
        }

        // Filter by duration range
        if (!empty($filters['duration_min'])) {
            $query->where('duration', '>=', (int) $filters['duration_min']);
        }

        if (!empty($filters['duration_max'])) {
            $query->where('duration', '<=', (int) $filters['duration_max']);
        }

        // Filter by release year
        if (!empty($filters['release_year'])) {
            $query->whereYear('release_date', $filters['release_year']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'release_date';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        // Validate sort fields
        $allowedSortFields = ['title', 'release_date', 'duration', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'release_date';
        }
        
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $movies = $query->paginate($filters['per_page'] ?? 15);

        // Filter theo computed status nếu có
        if (!empty($filters['status'])) {
            $statusFilter = strtoupper($filters['status']);
            $movies->setCollection(
                $movies->getCollection()->filter(function ($movie) use ($statusFilter) {
                    return $movie->getComputedStatus() === $statusFilter;
                })
            );
        }

        return $movies;
    }

    /**
     * Get all movies with filters
     * 
     * Lưu ý: filter status sẽ lọc theo computed status (dựa trên release_date)
     */
    public function getAllMovies(array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer']);

        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['release_date_from'])) {
            $query->where('release_date', '>=', $filters['release_date_from']);
        }

        if (isset($filters['release_date_to'])) {
            $query->where('release_date', '<=', $filters['release_date_to']);
        }

        $movies = $query->orderBy('release_date', 'desc')->paginate($filters['per_page'] ?? 15);

        // Filter theo computed status nếu có  
        if (isset($filters['status'])) {
            $statusFilter = strtoupper($filters['status']);
            $movies->setCollection(
                $movies->getCollection()->filter(function ($movie) use ($statusFilter) {
                    return $movie->getComputedStatus() === $statusFilter;
                })
            );
        }

        return $movies;
    }

    /**
     * Get movies by computed status
     * 
     * @param string $status COMING_SOON, NOW_SHOWING  
     */
    public function getMoviesByComputedStatus(string $status): Collection
    {
        $movies = Movie::with(['poster', 'trailer'])->get();

        return $movies->filter(function ($movie) use ($status) {
            return $movie->getComputedStatus() === strtoupper($status);
        });
    }

    /**
     * Get now showing movies
     * Phim có release_date <= today (không phụ thuộc vào showtimes)
     */
    public function getNowShowingMovies(): Collection
    {
        return $this->getMoviesByComputedStatus(Movie::STATUS_NOW_SHOWING);
    }

    /**
     * Get coming soon movies
     * Phim có release_date > today (không phụ thuộc vào showtimes)
     */
    public function getComingSoonMovies(): Collection
    {
        return $this->getMoviesByComputedStatus(Movie::STATUS_COMING_SOON);
    }

    /**
     * Get movie by ID with full details
     * Load: poster, trailer, showtimes, reviews, directors with avatar, actors with avatar
     */
    public function getMovieById(int $id): ?Movie
    {
        return Movie::with([
            'poster', 
            'trailer', 
            'showtimes.room.cinema', 
            'reviews.user', 
            'directors.avatar', 
            'actors.avatar'
        ])->find($id);
    }

    /**
     * Create a new movie
     * 
     * Status sẽ được tính tự động từ release_date (getComputedStatus)
     * Nếu có truyền status vào, sẽ lưu vào DB nhưng logic vẫn dùng computed status
     */
    public function createMovie(array $data): Movie
    {
        // Nếu không có status, tính từ release_date
        if (!isset($data['status']) && isset($data['release_date'])) {
            $releaseDate = \Carbon\Carbon::parse($data['release_date'])->startOfDay();
            $today = now()->startOfDay();
            $data['status'] = $releaseDate->lessThanOrEqualTo($today) 
                ? Movie::STATUS_NOW_SHOWING 
                : Movie::STATUS_COMING_SOON;
        } elseif (!isset($data['status'])) {
            // Nếu không có cả status và release_date, mặc định COMING_SOON
            $data['status'] = Movie::STATUS_COMING_SOON;
        }
        
        return Movie::create($data);
    }

    /**
     * Update movie
     * 
     * Nếu release_date thay đổi, status sẽ được tính lại tự động
     */
    public function updateMovie(int $id, array $data): bool
    {
        $movie = Movie::find($id);
        
        if (!$movie) {
            return false;
        }

        // Nếu release_date thay đổi, cập nhật status theo computed status
        if (isset($data['release_date'])) {
            $releaseDate = \Carbon\Carbon::parse($data['release_date'])->startOfDay();
            $today = now()->startOfDay();
            $data['status'] = $releaseDate->lessThanOrEqualTo($today) 
                ? Movie::STATUS_NOW_SHOWING 
                : Movie::STATUS_COMING_SOON;
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

    /**
     * Sync movie status based on release_date
     * 
     * Cập nhật cột status trong database dựa trên computed status (release_date)
     * Có thể gọi từ scheduler hoặc khi release_date thay đổi
     */
    public function syncMovieStatus(int $movieId): bool
    {
        $movie = Movie::find($movieId);
        
        if (!$movie) {
            return false;
        }

        $computedStatus = $movie->getComputedStatus();
        
        if ($movie->status !== $computedStatus) {
            $movie->status = $computedStatus;
            return $movie->save();
        }

        return true;
    }

    /**
     * Sync all movies status
     * 
     * Cập nhật status cho tất cả phim dựa trên release_date
     * Nên chạy bằng scheduler mỗi ngày hoặc khi có thay đổi release_date
     */
    public function syncAllMoviesStatus(): int
    {
        $movies = Movie::all();
        $updated = 0;

        foreach ($movies as $movie) {
            $computedStatus = $movie->getComputedStatus();
            
            if ($movie->status !== $computedStatus) {
                $movie->status = $computedStatus;
                $movie->save();
                $updated++;
            }
        }

        return $updated;
    }
}


