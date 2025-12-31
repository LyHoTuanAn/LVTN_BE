<?php

namespace App\Services\Review;

use App\Models\Review;
use App\Models\Movie;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewService
{
    /**
     * Get a specific review by ID
     */
    public function getReviewById(int $reviewId): ?Review
    {
        return Review::with(['user', 'movie.poster', 'media', 'booking'])->find($reviewId);
    }

    /**
     * Create a new review
     * 
     * @throws \Exception
     */
    public function createReview(int $userId, array $data): Review
    {
        return DB::transaction(function () use ($userId, $data) {
            $movieId = $data['movie_id'];
            $bookingId = $data['booking_id'] ?? null;

            // Check if movie exists
            $movie = Movie::find($movieId);
            if (!$movie) {
                throw new \Exception('MOVIE_NOT_FOUND');
            }

            // Check if user already reviewed this movie
            $existingReview = Review::where('user_id', $userId)
                ->where('movie_id', $movieId)
                ->first();

            if ($existingReview) {
                throw new \Exception('ALREADY_REVIEWED');
            }

            // If booking_id provided, validate it
            if ($bookingId) {
                $booking = Booking::with('showtime')->find($bookingId);

                if (!$booking) {
                    throw new \Exception('BOOKING_NOT_FOUND');
                }

                // Check if booking belongs to user
                if ($booking->user_id !== $userId) {
                    throw new \Exception('BOOKING_NOT_BELONG_TO_USER');
                }

                // Check if booking is for this movie
                if ($booking->showtime->movie_id !== $movieId) {
                    throw new \Exception('BOOKING_MOVIE_MISMATCH');
                }

                // Check if showtime has ended (user can only review after watching)
                $showtimeEnd = Carbon::parse($booking->showtime->date . ' ' . $booking->showtime->end_time);
                if ($showtimeEnd->isFuture()) {
                    throw new \Exception('SHOWTIME_NOT_ENDED');
                }

                // Check if booking is completed (paid and confirmed)
                if ($booking->status !== 'completed') {
                    throw new \Exception('BOOKING_NOT_COMPLETED');
                }
            }

            // Create review
            $review = Review::create([
                'user_id' => $userId,
                'movie_id' => $movieId,
                'booking_id' => $bookingId,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'media_id' => $data['media_id'] ?? null,
                'status' => 'approved', // Auto approve, can change to 'pending' for moderation
            ]);

            return $review->load(['user', 'movie.poster', 'media']);
        });
    }
}
