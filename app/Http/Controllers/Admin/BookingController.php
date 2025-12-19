<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'is_paid', 'showtime_id', 'search']);
        
        // Handle search by booking code or user email
        if ($request->has('search') && $request->search) {
            $bookings = $this->bookingService->getAllBookings($filters);
        } else {
            $bookings = $this->bookingService->getAllBookings($filters);
        }

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking
     */
    public function show(int $id)
    {
        $booking = $this->bookingService->getBookingById($id);

        if (!$booking) {
            abort(404, __('Booking not found'));
        }

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled,completed',
        ]);

        $booking = $this->bookingService->getBookingById($id);

        if (!$booking) {
            abort(404, __('Booking not found'));
        }

        $booking->status = $request->status;
        $booking->save();

        return redirect()
            ->route('admin.bookings.show', $id)
            ->with('success', __('Booking status updated successfully'));
    }

    /**
     * Update payment status
     */
    public function updatePayment(Request $request, int $id)
    {
        $request->validate([
            'is_paid' => 'required|boolean',
            'payment_method' => 'nullable|string|max:255',
        ]);

        $booking = $this->bookingService->getBookingById($id);

        if (!$booking) {
            abort(404, __('Booking not found'));
        }

        $booking->is_paid = $request->is_paid;
        if ($request->has('payment_method')) {
            $booking->payment_method = $request->payment_method;
        }
        $booking->save();

        return redirect()
            ->route('admin.bookings.show', $id)
            ->with('success', __('Payment status updated successfully'));
    }
}
