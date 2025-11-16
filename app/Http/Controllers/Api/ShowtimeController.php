<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowtimeResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Showtime\ShowtimeService;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    use ApiResponseTrait;

    protected ShowtimeService $showtimeService;

    public function __construct(ShowtimeService $showtimeService)
    {
        $this->showtimeService = $showtimeService;
    }

    /**
     * Get all showtimes
     */
    public function index(Request $request)
    {
        $showtimes = $this->showtimeService->getAllShowtimes($request->all());

        return $this->successResponse(
            'SHOWTIMES_FETCHED_SUCCESS',
            ShowtimeResource::collection($showtimes)
        );
    }

    /**
     * Get showtime by ID
     */
    public function show($id)
    {
        $showtime = $this->showtimeService->getShowtimeById($id);

        if (!$showtime) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        return $this->successResponse(
            'SHOWTIME_FETCHED_SUCCESS',
            new ShowtimeResource($showtime)
        );
    }
}
