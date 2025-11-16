<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CinemaResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Cinema\CinemaService;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    use ApiResponseTrait;

    protected CinemaService $cinemaService;

    public function __construct(CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    /**
     * Get all cinemas
     */
    public function index(Request $request)
    {
        $cinemas = $this->cinemaService->getAllCinemas($request->all());

        return $this->successResponse(
            'CINEMAS_FETCHED_SUCCESS',
            CinemaResource::collection($cinemas)
        );
    }

    /**
     * Get cinema by ID
     */
    public function show($id)
    {
        $cinema = $this->cinemaService->getCinemaById($id);

        if (!$cinema) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        return $this->successResponse(
            'CINEMA_FETCHED_SUCCESS',
            new CinemaResource($cinema)
        );
    }
}
