<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QrTicketController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display the QR/Barcode scanner page
     */
    public function index()
    {
        return view('admin.qr-ticket.index');
    }

    /**
     * Process the scanned barcode and mark booking as checked in
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->code));
        
        // Find booking by code
        $booking = $this->bookingService->getBookingByCode($code);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'code' => 'BOOKING_NOT_FOUND',
                'message' => __('errors.BOOKING_NOT_FOUND'),
            ], 404);
        }

        if ($booking->checked_in) {
            return response()->json([
                'success' => false,
                'code' => 'BOOKING_ALREADY_CHECKED_IN',
                'message' => __('errors.BOOKING_ALREADY_CHECKED_IN'),
                'data' => [
                    'booking' => $this->formatBookingData($booking),
                ],
            ], 400);
        }

        // Check if booking is canceled
        if ($booking->status === 'canceled') {
            return response()->json([
                'success' => false,
                'code' => 'BOOKING_CANCELED',
                'message' => __('errors.BOOKING_CANCELED'),
                'data' => [
                    'booking' => $this->formatBookingData($booking),
                ],
            ], 400);
        }

        // Check if booking is paid
        if (!$booking->is_paid) {
            return response()->json([
                'success' => false,
                'code' => 'BOOKING_NOT_PAID',
                'message' => __('errors.BOOKING_NOT_PAID'),
                'data' => [
                    'booking' => $this->formatBookingData($booking),
                ],
            ], 400);
        }

        $booking->checked_in = true;
        $booking->checked_in_at = now();
        $booking->save();

        // Reload booking with relationships
        $booking->load(['user', 'showtime.movie', 'showtime.room', 'seats', 'voucher']);

        return response()->json([
            'success' => true,
            'code' => 'BOOKING_CHECKED_IN_SUCCESS',
            'message' => __('success.BOOKING_CHECKED_IN_SUCCESS'),
            'data' => [
                'booking' => $this->formatBookingData($booking),
            ],
        ]);
    }

    /**
     * Format booking data for response
     */
    protected function formatBookingData($booking): array
    {
        return [
            'id' => $booking->id,
            'code' => $booking->code,
            'status' => $booking->status,
            'is_paid' => $booking->is_paid,
            'checked_in' => $booking->checked_in ?? false,
            'checked_in_at' => $booking->checked_in_at ? $booking->checked_in_at->format('d/m/Y H:i') : null,
            'price' => number_format($booking->price, 0, ',', '.') . ' VND',
            'total_price' => number_format($booking->total_price, 0, ',', '.') . ' VND',
            'voucher_amount' => $booking->voucher_amount ? number_format($booking->voucher_amount, 0, ',', '.') . ' VND' : null,
            'payment_method' => $booking->payment_method,
            'created_at' => $booking->created_at->format('d/m/Y H:i'),
            'user' => $booking->user ? [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone ?? null,
            ] : null,
            'showtime' => $booking->showtime ? [
                'date' => $booking->showtime->date->format('d/m/Y'),
                'start_time' => $booking->showtime->start_time,
                'end_time' => $booking->showtime->end_time,
                'movie' => $booking->showtime->movie ? [
                    'title' => $booking->showtime->movie->title,
                ] : null,
                'room' => $booking->showtime->room ? [
                    'name' => $booking->showtime->room->name,
                ] : null,
            ] : null,
            'seats' => $booking->seats->map(function ($seat) {
                return $seat->row . $seat->number;
            })->implode(', '),
        ];
    }

    /**
     * Quick lookup booking by code (for manual search)
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->code));
        
        $booking = $this->bookingService->getBookingByCode($code);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'code' => 'BOOKING_NOT_FOUND',
                'message' => __('errors.BOOKING_NOT_FOUND'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 'BOOKING_FOUND',
            'message' => __('success.BOOKING_FOUND'),
            'data' => [
                'booking' => $this->formatBookingData($booking),
            ],
        ]);
    }

    /**
     * Get recent scans (checked-in bookings)
     */
    public function recentScans(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        
        $recentScans = $this->bookingService->getRecentScans($limit);

        return response()->json([
            'success' => true,
            'code' => 'RECENT_SCANS_FETCHED_SUCCESS',
            'message' => __('success.RECENT_SCANS_FETCHED_SUCCESS'),
            'data' => [
                'scans' => $recentScans,
            ],
        ]);
    }
}
