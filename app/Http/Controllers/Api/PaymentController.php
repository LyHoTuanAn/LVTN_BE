<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentIntentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Booking;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a payment intent for a booking
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPaymentIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
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
            $booking = Booking::with(['user', 'showtime.movie'])->findOrFail($request->booking_id);

            // Check if booking belongs to authenticated user
            if ($booking->user_id !== auth()->id()) {
                return $this->errorResponse(
                    'FORBIDDEN',
                    [],
                    null,
                    403
                );
            }

            // Check if booking is already paid
            if ($booking->is_paid) {
                return $this->errorResponse(
                    'BOOKING_ALREADY_PAID',
                    [],
                    null,
                    400
                );
            }

            // Check if booking status is pending
            if ($booking->status !== 'pending') {
                return $this->errorResponse(
                    'BOOKING_INVALID_STATUS',
                    [],
                    null,
                    400
                );
            }

            // Create payment intent with Stripe
            $paymentIntent = $this->stripeService->createPaymentIntent($booking, [
                'movie_title' => $booking->showtime->movie->title ?? 'Unknown Movie',
            ]);

            // Update booking with payment intent ID
            $booking->update([
                'payment_intent_id' => $paymentIntent->id,
                'payment_method' => 'stripe',
            ]);

            return $this->successResponse(
                'PAYMENT_INTENT_CREATED_SUCCESS',
                new PaymentIntentResource([
                    'id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'status' => $paymentIntent->status,
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->code,
                ])
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'PAYMENT_INTENT_CREATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                400
            );
        }
    }

    /**
     * Confirm payment after client-side payment completion
     * Supports both Checkout Session (session_id) and Payment Intent (payment_intent_id)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'session_id' => 'nullable|string',
            'payment_intent_id' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->session_id && !$request->payment_intent_id) {
                $validator->errors()->add('session_id', 'Either session_id or payment_intent_id is required');
            }
        });

        if ($validator->fails()) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                $validator->errors()->toArray(),
                null,
                422
            );
        }

        try {
            return DB::transaction(function () use ($request) {
                $booking = Booking::where('id', $request->booking_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                // If already paid, return success
                if ($booking->is_paid) {
                    return $this->successResponse(
                        'PAYMENT_CONFIRMED_SUCCESS',
                        [
                            'booking_id' => $booking->id,
                            'booking_code' => $booking->code,
                            'status' => $booking->status,
                            'is_paid' => $booking->is_paid,
                        ]
                    );
                }

                $paymentConfirmed = false;

                // Check Checkout Session (primary method for new flow)
                if ($request->session_id) {
                    // Verify session ID matches booking
                    if ($booking->payment_intent_id !== $request->session_id) {
                        return $this->errorResponse(
                            'PAYMENT_INTENT_MISMATCH',
                            [],
                            null,
                            400
                        );
                    }
                    $paymentConfirmed = $this->stripeService->isCheckoutSessionPaid($request->session_id);
                }
                // Fallback: Check Payment Intent
                elseif ($request->payment_intent_id) {
                    if ($booking->payment_intent_id !== $request->payment_intent_id) {
                        return $this->errorResponse(
                            'PAYMENT_INTENT_MISMATCH',
                            [],
                            null,
                            400
                        );
                    }
                    $paymentConfirmed = $this->stripeService->isPaymentSuccessful($request->payment_intent_id);
                }

                if (!$paymentConfirmed) {
                    return $this->errorResponse(
                        'PAYMENT_NOT_COMPLETED',
                        [],
                        null,
                        400
                    );
                }

                // Update booking as paid
                $booking->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'paid_at' => now(),
                ]);

                return $this->successResponse(
                    'PAYMENT_CONFIRMED_SUCCESS',
                    [
                        'booking_id' => $booking->id,
                        'booking_code' => $booking->code,
                        'status' => $booking->status,
                        'is_paid' => $booking->is_paid,
                    ]
                );
            });
        } catch (\Exception $e) {
            return $this->errorResponse(
                'PAYMENT_CONFIRMATION_FAILED',
                ['error' => $e->getMessage()],
                null,
                400
            );
        }
    }

    /**
     * Get payment status for a booking
     *
     * @param int $bookingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentStatus($bookingId)
    {
        try {
            $booking = Booking::where('id', $bookingId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $stripeStatus = null;
            if ($booking->payment_intent_id) {
                $stripeStatus = $this->stripeService->getPaymentStatus($booking->payment_intent_id);
            }

            return $this->successResponse(
                'PAYMENT_STATUS_FETCHED_SUCCESS',
                [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->code,
                    'is_paid' => $booking->is_paid,
                    'booking_status' => $booking->status,
                    'payment_method' => $booking->payment_method,
                    'stripe_status' => $stripeStatus,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'NOT_FOUND',
                [],
                null,
                404
            );
        }
    }

    /**
     * Cancel payment and refund if applicable
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
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
            return DB::transaction(function () use ($request) {
                $booking = Booking::where('id', $request->booking_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                // If payment intent exists and not yet paid, cancel it
                if ($booking->payment_intent_id && !$booking->is_paid) {
                    try {
                        $this->stripeService->cancelPaymentIntent($booking->payment_intent_id);
                    } catch (\Exception $e) {
                        // Payment intent might already be canceled or expired
                    }
                }

                // If already paid, create a refund
                if ($booking->is_paid && $booking->payment_intent_id) {
                    $this->stripeService->createRefund($booking->payment_intent_id);
                }

                // Update booking status
                $booking->update([
                    'status' => 'canceled',
                    'is_paid' => false,
                ]);

                return $this->successResponse(
                    'PAYMENT_CANCELLED_SUCCESS',
                    [
                        'booking_id' => $booking->id,
                        'booking_code' => $booking->code,
                        'status' => $booking->status,
                    ]
                );
            });
        } catch (\Exception $e) {
            return $this->errorResponse(
                'PAYMENT_CANCEL_FAILED',
                ['error' => $e->getMessage()],
                null,
                400
            );
        }
    }

    /**
     * Get Stripe publishable key for client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublishableKey()
    {
        return $this->successResponse(
            'STRIPE_KEY_FETCHED_SUCCESS',
            [
                'publishable_key' => config('stripe.key'),
            ]
        );
    }
}
