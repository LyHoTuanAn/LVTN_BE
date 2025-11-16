<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Movie\MovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ApiResponseTrait;

    protected MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Get all movies
     */
    public function index(Request $request)
    {
        $movies = $this->movieService->getAllMovies($request->all());

        return $this->successResponse(
            'MOVIES_FETCHED_SUCCESS',
            MovieResource::collection($movies)
        );
    }

    /**
     * Get movie by ID
     */
    public function show($id)
    {
        $movie = $this->movieService->getMovieById($id);

        if (!$movie) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        return $this->successResponse(
            'MOVIE_FETCHED_SUCCESS',
            new MovieResource($movie)
        );
    }
}
