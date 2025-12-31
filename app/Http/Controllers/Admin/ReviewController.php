<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Review\ReviewService;
use App\Models\Review;
use App\Models\Movie;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = Review::query()
            ->with(['user', 'movie', 'media'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by movie
        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }

        // Search by user name or comment
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(15)->appends($request->query());
        
        // Get movies for filter dropdown
        $movies = Movie::select('id', 'title')->orderBy('title')->get();

        return view('admin.reviews.index', compact('reviews', 'movies'));
    }

    /**
     * Display the specified review
     */
    public function show(int $id)
    {
        $review = Review::with(['user', 'movie.poster', 'booking.showtime.room', 'media'])->find($id);

        if (!$review) {
            abort(404, __('Review not found'));
        }

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Update review status
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review = Review::find($id);

        if (!$review) {
            abort(404, __('Review not found'));
        }

        $review->status = $request->status;
        $review->save();

        return redirect()
            ->route('admin.reviews.show', $id)
            ->with('success', __('Review status updated successfully'));
    }

    /**
     * Delete a review
     */
    public function destroy(int $id)
    {
        $review = Review::find($id);

        if (!$review) {
            abort(404, __('Review not found'));
        }

        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', __('Review deleted successfully'));
    }
}
