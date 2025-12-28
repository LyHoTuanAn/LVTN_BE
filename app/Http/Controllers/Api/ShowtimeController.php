<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowtimeResource;
use App\Http\Resources\SeatResource;
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

    /**
     * Get seats with booking status for a showtime
     * 
     * @param int $id Showtime ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function seats($id)
    {
        $result = $this->showtimeService->getSeatsWithStatus($id);

        if (!$result) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        // Transform seats to include booking_status
        $seatsData = $result['seats']->map(function ($seat) {
            return [
                'id' => $seat->id,
                'row' => $seat->row,
                'number' => $seat->number,
                'type' => $seat->type,
                'status' => $seat->status,
                'booking_status' => $seat->booking_status,
            ];
        });

        // Transform seats by row
        $seatsByRow = $result['seats_by_row']->map(function ($rowData) {
            return [
                'row' => $rowData['row'],
                'seats' => $rowData['seats']->map(function ($seat) {
                    return [
                        'id' => $seat->id,
                        'row' => $seat->row,
                        'number' => $seat->number,
                        'type' => $seat->type,
                        'status' => $seat->status,
                        'booking_status' => $seat->booking_status,
                    ];
                }),
            ];
        });

        return $this->successResponse(
            'SHOWTIME_SEATS_FETCHED_SUCCESS',
            [
                'showtime' => new ShowtimeResource($result['showtime']),
                'seats' => $seatsData,
                'seats_by_row' => $seatsByRow,
                'summary' => [
                    'total' => $result['total_count'],
                    'available' => $result['available_count'],
                    'booked' => $result['booked_count'],
                ],
            ]
        );
    }
}

