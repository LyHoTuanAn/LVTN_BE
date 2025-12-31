<?php

namespace App\Services\FavoriteMovie;

use App\Models\FavoriteMovie;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;

class FavoriteMovieService
{
    /**
     * Lấy danh sách phim yêu thích của user
     *
     * @param int $userId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFavoriteMovies(int $userId, array $filters = [])
    {
        $query = Movie::query()
            ->whereHas('favoritedByUsers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['poster', 'trailer']);

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by title
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        if ($sortBy === 'favorited_at') {
            $query->withPivot(['created_at as favorited_at'])
                ->orderBy('favorite_movies.created_at', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * Thêm phim vào danh sách yêu thích
     *
     * @param int $userId
     * @param int $movieId
     * @return array ['success' => bool, 'message' => string, 'data' => ?FavoriteMovie]
     */
    public function addFavorite(int $userId, int $movieId): array
    {
        // Kiểm tra phim tồn tại
        $movie = Movie::find($movieId);
        if (!$movie) {
            return [
                'success' => false,
                'code' => 'MOVIE_NOT_FOUND',
            ];
        }

        // Kiểm tra đã yêu thích chưa
        $exists = FavoriteMovie::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->exists();

        if ($exists) {
            return [
                'success' => false,
                'code' => 'MOVIE_ALREADY_FAVORITED',
            ];
        }

        // Thêm vào yêu thích
        $favorite = FavoriteMovie::create([
            'user_id' => $userId,
            'movie_id' => $movieId,
        ]);

        return [
            'success' => true,
            'code' => 'FAVORITE_ADDED_SUCCESS',
            'data' => $favorite,
        ];
    }

    /**
     * Xóa phim khỏi danh sách yêu thích
     *
     * @param int $userId
     * @param int $movieId
     * @return array ['success' => bool, 'code' => string]
     */
    public function removeFavorite(int $userId, int $movieId): array
    {
        $deleted = FavoriteMovie::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->delete();

        if (!$deleted) {
            return [
                'success' => false,
                'code' => 'FAVORITE_NOT_FOUND',
            ];
        }

        return [
            'success' => true,
            'code' => 'FAVORITE_REMOVED_SUCCESS',
        ];
    }

}
