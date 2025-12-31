<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Review\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Create a new review
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|integer|exists:movies,id',
            'booking_id' => 'nullable|integer|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'media_id' => 'nullable|integer|exists:media_files,id',
        ]);

        try {
            $userId = auth()->id();
            $review = $this->reviewService->createReview($userId, $request->all());

            return $this->successResponse(
                'REVIEW_CREATED_SUCCESS',
                new ReviewResource($review),
                __('success.REVIEW_CREATED_SUCCESS'),
                201
            );
        } catch (\Exception $e) {
            $errorCode = $e->getMessage();
            return $this->errorResponse(
                $errorCode,
                __('errors.' . $errorCode),
                400
            );
        }
    }

    /**
     * Get a specific review
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->getReviewById($id);

        if (!$review) {
            return $this->errorResponse(
                'REVIEW_NOT_FOUND',
                __('errors.REVIEW_NOT_FOUND'),
                404
            );
        }

        return $this->successResponse(
            'REVIEW_FETCHED_SUCCESS',
            new ReviewResource($review),
            __('success.REVIEW_FETCHED_SUCCESS')
        );
    }
}
