<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteMovieResource;
use App\Http\Resources\MovieResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\FavoriteMovie\FavoriteMovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteMovieController extends Controller
{
    use ApiResponseTrait;

    protected FavoriteMovieService $favoriteMovieService;

    public function __construct(FavoriteMovieService $favoriteMovieService)
    {
        $this->favoriteMovieService = $favoriteMovieService;
    }

    /**
     * Lấy danh sách phim yêu thích của user đang đăng nhập
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $movies = $this->favoriteMovieService->getFavoriteMovies($userId, $request->all());

        return $this->successResponse(
            'FAVORITES_FETCHED_SUCCESS',
            FavoriteMovieResource::collection($movies)->response()->getData(true),
        );
    }

    /**
     * Thêm phim vào danh sách yêu thích
     *
     * @param int $movieId
     * @return JsonResponse
     */
    public function store(int $movieId): JsonResponse
    {
        $userId = auth()->id();
        $result = $this->favoriteMovieService->addFavorite($userId, $movieId);

        if (!$result['success']) {
            $statusCode = $result['code'] === 'MOVIE_NOT_FOUND' ? 404 : 400;
            return $this->errorResponse($result['code'], [], null, $statusCode);
        }

        return $this->successResponse(
            $result['code'],
            [
                'movie_id' => $movieId,
                'is_favorited' => true,
            ],
            null,
            201
        );
    }

    /**
     * Xóa phim khỏi danh sách yêu thích
     *
     * @param int $movieId
     * @return JsonResponse
     */
    public function destroy(int $movieId): JsonResponse
    {
        $userId = auth()->id();
        $result = $this->favoriteMovieService->removeFavorite($userId, $movieId);

        if (!$result['success']) {
            return $this->errorResponse($result['code'], [], null, 404);
        }

        return $this->successResponse(
            $result['code'],
            [
                'movie_id' => $movieId,
                'is_favorited' => false,
            ]
        );
    }

}
