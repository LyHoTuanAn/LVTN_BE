<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShowtimeRequest;
use App\Http\Requests\Admin\UpdateShowtimeRequest;
use App\Models\Movie;
use App\Models\Room;
use App\Services\Showtime\ShowtimeService;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    protected ShowtimeService $showtimeService;

    public function __construct(ShowtimeService $showtimeService)
    {
        $this->showtimeService = $showtimeService;
    }

    /**
     * Display a listing of showtimes
     */
    public function index(Request $request)
    {
        $showtimes = $this->showtimeService->getAllShowtimes($request->all());
        $movies = Movie::orderBy('title')->get();
        $rooms = Room::orderBy('name')->get();

        return view('admin.showtimes.index', compact('showtimes', 'movies', 'rooms'));
    }

    /**
     * Show the form for creating a new showtime
     */
    public function create()
    {
        $movies = Movie::where('status', '!=', 'ended')->orderBy('title')->get();
        $rooms = Room::orderBy('name')->get();

        return view('admin.showtimes.create', compact('movies', 'rooms'));
    }

    /**
     * Store a newly created showtime in storage
     */
    public function store(StoreShowtimeRequest $request)
    {
        try {
            $data = $request->validated();

            $showtime = $this->showtimeService->createShowtime($data);

            return redirect()
                ->route('admin.showtimes.show', $showtime->id)
                ->with('success', __('Showtime created successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to create showtime: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Display the specified showtime
     */
    public function show(int $id)
    {
        $showtime = $this->showtimeService->getShowtimeById($id);

        if (!$showtime) {
            abort(404, __('Showtime not found'));
        }

        // Load seats with booking status for this showtime
        $showtime->load(['room.seats', 'bookings.seats']);
        
        // Get booked seat IDs for this showtime
        $bookedSeatIds = $showtime->bookings()
            ->where('status', '!=', 'canceled')
            ->get()
            ->pluck('seats')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        return view('admin.showtimes.show', compact('showtime', 'bookedSeatIds'));
    }

    /**
     * Display seat map for showtime
     */
    public function seatMap(int $id)
    {
        $showtime = $this->showtimeService->getShowtimeById($id);

        if (!$showtime) {
            abort(404, __('Showtime not found'));
        }

        // Load seats with booking status for this showtime
        $showtime->load(['room.seats', 'bookings.seats', 'bookings.user']);
        
        // Get booked seats with booking info
        $bookedSeats = [];
        foreach ($showtime->bookings()->where('status', '!=', 'canceled')->get() as $booking) {
            foreach ($booking->seats as $seat) {
                $bookedSeats[$seat->id] = [
                    'booking_code' => $booking->code,
                    'user_name' => $booking->user->name,
                    'is_paid' => $booking->is_paid,
                ];
            }
        }

        // Group seats by row
        $seatsByRow = $showtime->room->seats->groupBy('row')->sortKeys();

        return view('admin.showtimes.seat-map', compact('showtime', 'seatsByRow', 'bookedSeats'));
    }

    /**
     * Show the form for editing the specified showtime
     */
    public function edit(int $id)
    {
        $showtime = $this->showtimeService->getShowtimeById($id);

        if (!$showtime) {
            abort(404, __('Showtime not found'));
        }

        $movies = Movie::orderBy('title')->get();
        $rooms = Room::orderBy('name')->get();

        return view('admin.showtimes.edit', compact('showtime', 'movies', 'rooms'));
    }

    /**
     * Update the specified showtime in storage
     */
    public function update(UpdateShowtimeRequest $request, int $id)
    {
        try {
            $showtime = $this->showtimeService->getShowtimeById($id);

            if (!$showtime) {
                abort(404, __('Showtime not found'));
            }

            $data = $request->validated();
            $this->showtimeService->updateShowtime($id, $data);

            return redirect()
                ->route('admin.showtimes.show', $id)
                ->with('success', __('Showtime updated successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to update showtime: :message', ['message' => $e->getMessage()])])
                ->withInput();
        }
    }

    /**
     * Remove the specified showtime from storage
     */
    public function destroy(int $id)
    {
        try {
            $showtime = $this->showtimeService->getShowtimeById($id);

            if (!$showtime) {
                abort(404, __('Showtime not found'));
            }

            // Check if showtime has bookings
            if ($showtime->bookings && $showtime->bookings->count() > 0) {
                return back()->withErrors(['error' => __('Cannot delete showtime with existing bookings')]);
            }

            $this->showtimeService->deleteShowtime($id);

            return redirect()
                ->route('admin.showtimes.index')
                ->with('success', __('Showtime deleted successfully'));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => __('Failed to delete showtime: :message', ['message' => $e->getMessage()])]);
        }
    }
}
