<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CalculateTicketPriceRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\TicketPriceResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    use ApiResponseTrait;

    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Calculate ticket price for preview
     */
    public function calculatePrice(CalculateTicketPriceRequest $request)
    {
        try {
            $data = $this->bookingService->calculateTicketPrice(
                $request->validated(),
                auth()->id()
            );

            return $this->successResponse(
                'TICKET_PRICE_CALCULATED_SUCCESS',
                new TicketPriceResource($data)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'TICKET_PRICE_CALCULATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                400
            );
        }
    }

    /**
     * Get all bookings for authenticated user
     */
    public function index(Request $request)
    {
        $bookings = $this->bookingService->getAllBookings([
            'user_id' => auth()->id(),
            ...$request->all(),
        ]);

        return $this->successResponse(
            'BOOKINGS_FETCHED_SUCCESS',
            BookingResource::collection($bookings)
        );
    }

    /**
     * Create a new booking with payment (Stripe or VNPay)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'voucher_code' => 'nullable|exists:vouchers,code',
            'payment_method' => 'nullable|in:stripe,vnpay',
            'bank_code' => 'nullable|string', // VNPay bank code (optional)
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                null,
                422
            );
        }

        try {
            $validated = $validator->validated();
            $paymentMethod = $validated['payment_method'] ?? 'stripe';
            $ipAddress = $request->ip();

            $result = $this->bookingService->createBooking(
                $validated,
                auth()->id(),
                $paymentMethod,
                $ipAddress
            );

            return $this->successResponse(
                'BOOKING_CREATED_SUCCESS',
                [
                    'booking' => new BookingResource($result['booking']),
                    'payment' => $result['payment'],
                ],
                null,
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'BOOKING_CREATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                400
            );
        }
    }

    /**
     * Get booking by ID
     */
    public function show($id)
    {
        $booking = $this->bookingService->getBookingById((int) $id);

        if (!$booking || $booking->user_id != auth()->id()) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }

        return $this->successResponse(
            'BOOKING_FETCHED_SUCCESS',
            new BookingResource($booking)
        );
    }

    /**
     * Cancel booking
     */
    public function cancel($id)
    {
        $result = $this->bookingService->cancelBooking((int) $id, auth()->id());

        if (!$result) {
            return $this->errorResponse(
                'BOOKING_CANCEL_FAILED',
                [],
                null,
                400
            );
        }

        return $this->successResponse('BOOKING_CANCELLED_SUCCESS');
    }
}
