<?php

namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieService
{
    /**
     * Get all movies with filters
     * 
     * Lưu ý: filter status sẽ lọc theo computed status (dựa trên showtimes)
     */
    public function getAllMovies(array $filters = []): LengthAwarePaginator
    {
        $query = Movie::query()->with(['poster', 'trailer', 'showtimes']);

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
     * @param string $status COMING_SOON, UPCOMING, NOW_SHOWING  
     */
    public function getMoviesByComputedStatus(string $status): Collection
    {
        $movies = Movie::with(['poster', 'trailer', 'showtimes'])->get();

        return $movies->filter(function ($movie) use ($status) {
            return $movie->getComputedStatus() === strtoupper($status);
        });
    }

    /**
     * Get now showing movies
     * Phim có ít nhất 1 suất đang ONGOING hoặc có suất SCHEDULED trong ngày hôm nay
     */
    public function getNowShowingMovies(): Collection
    {
        return $this->getMoviesByComputedStatus(Movie::STATUS_NOW_SHOWING);
    }

    /**
     * Get upcoming movies 
     * Phim có suất SCHEDULED trong tương lai (sau hôm nay)
     */
    public function getUpcomingMovies(): Collection
    {
        return $this->getMoviesByComputedStatus(Movie::STATUS_UPCOMING);
    }

    /**
     * Get coming soon movies
     * Phim chưa có suất chiếu nào
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
     */
    public function createMovie(array $data): Movie
    {
        // Mặc định status là COMING_SOON khi tạo mới
        if (!isset($data['status'])) {
            $data['status'] = Movie::STATUS_COMING_SOON;
        }
        
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

    /**
     * Sync movie status based on showtimes
     * 
     * Cập nhật cột status trong database dựa trên computed status
     * Có thể gọi từ scheduler hoặc khi showtime thay đổi
     */
    public function syncMovieStatus(int $movieId): bool
    {
        $movie = Movie::with('showtimes')->find($movieId);
        
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
     * Cập nhật status cho tất cả phim dựa trên showtimes
     * Nên chạy bằng scheduler mỗi phút hoặc khi có thay đổi showtime
     */
    public function syncAllMoviesStatus(): int
    {
        $movies = Movie::with('showtimes')->get();
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


