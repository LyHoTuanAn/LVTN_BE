<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Resources\ShowtimeResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Movie\MovieService;
use App\Services\Showtime\ShowtimeService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ApiResponseTrait;

    protected MovieService $movieService;
    protected ShowtimeService $showtimeService;

    public function __construct(MovieService $movieService, ShowtimeService $showtimeService)
    {
        $this->movieService = $movieService;
        $this->showtimeService = $showtimeService;
    }

    /**
     * Search movies with advanced filters
     * 
     * @param Request $request
     *   - keyword: Search in title and description
     *   - genre: Filter by genre
     *   - status: Filter by computed status (COMING_SOON, NOW_SHOWING) - based on release_date
     *   - age_classification: Filter by age classification (P, K, T13, T16, T18, C)
     *   - duration_min: Minimum duration in minutes
     *   - duration_max: Maximum duration in minutes
     *   - release_year: Filter by release year
     *   - sort_by: Sort field (title, release_date, duration, created_at)
     *   - sort_order: Sort order (asc, desc)
     *   - per_page: Items per page (default 15)
     */
    public function search(Request $request)
    {
        $movies = $this->movieService->searchMovies($request->all());

        return $this->successResponse(
            'MOVIES_SEARCH_SUCCESS',
            MovieResource::collection($movies)
        );
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

    /**
     * Get showtimes for a movie
     * 
     * @param int $id Movie ID
     * @param Request $request
     *   - date: Filter by specific date (Y-m-d format)
     *   - date_from: Filter from date
     *   - date_to: Filter to date
     *   - group_by_date: If true, group showtimes by date
     */
    public function showtimes($id, Request $request)
    {
        // Check if movie exists
        $movie = $this->movieService->getMovieById($id);

        if (!$movie) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        // Get showtimes with filters
        $filters = [
            'date' => $request->get('date'),
            'room_type_id' => $request->get('room_type_id'),
        ];
        $showtimes = $this->showtimeService->getShowtimesByMovie($id, $filters);

        // Optional: Filter by date range
        if ($request->has('date_from')) {
            $showtimes = $showtimes->filter(function ($showtime) use ($request) {
                return $showtime->date >= $request->get('date_from');
            });
        }

        if ($request->has('date_to')) {
            $showtimes = $showtimes->filter(function ($showtime) use ($request) {
                return $showtime->date <= $request->get('date_to');
            });
        }

        // Group by date if requested
        if ($request->boolean('group_by_date')) {
            $grouped = $showtimes->groupBy(function ($showtime) {
                return $showtime->date->format('Y-m-d');
            })->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'showtimes' => ShowtimeResource::collection($items),
                ];
            })->values();

            return $this->successResponse(
                'MOVIE_SHOWTIMES_FETCHED_SUCCESS',
                [
                    'movie' => [
                        'id' => $movie->id,
                        'title' => $movie->title,
                    ],
                    'schedule' => $grouped,
                ]
            );
        }

        return $this->successResponse(
            'MOVIE_SHOWTIMES_FETCHED_SUCCESS',
            [
                'movie' => [
                    'id' => $movie->id,
                    'title' => $movie->title,
                ],
                'showtimes' => ShowtimeResource::collection($showtimes),
            ]
        );
    }
}

