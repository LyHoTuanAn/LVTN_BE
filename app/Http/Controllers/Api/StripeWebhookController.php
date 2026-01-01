<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Notification\NotificationDispatcher;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected StripeService $stripeService;
    protected NotificationDispatcher $notificationDispatcher;

    public function __construct(StripeService $stripeService, NotificationDispatcher $notificationDispatcher)
    {
        $this->stripeService = $stripeService;
        $this->notificationDispatcher = $notificationDispatcher;
    }

    /**
     * Handle Stripe webhook events
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received: ' . $event->type);

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'checkout.session.expired':
                $this->handleCheckoutSessionExpired($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'payment_intent.canceled':
                $this->handlePaymentIntentCanceled($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle checkout.session.completed event
     * This is the PRIMARY event for Stripe Checkout payments
     *
     * @param \Stripe\Checkout\Session $session
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $bookingId = $session->metadata->booking_id ?? null;

        if (!$bookingId) {
            Log::warning('Checkout session completed but no booking_id in metadata: ' . $session->id);
            return;
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            Log::warning('Booking not found for checkout session: ' . $session->id);
            return;
        }

        // Only update if payment is complete
        if ($session->payment_status === 'paid' && !$booking->is_paid) {
            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'paid_at' => now(),
                'payment_intent_id' => $session->payment_intent ?? $session->id,
            ]);

            Log::info("Booking #{$booking->code} marked as paid via Checkout webhook");

            // Gửi thông báo qua Telegram và FCM
            try {
                $this->notificationDispatcher->dispatchBookingPaid($booking);
            } catch (\Exception $e) {
                Log::error("Failed to send booking paid notification", [
                    'booking_code' => $booking->code,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle checkout.session.expired event
     *
     * @param \Stripe\Checkout\Session $session
     */
    protected function handleCheckoutSessionExpired($session)
    {
        $bookingId = $session->metadata->booking_id ?? null;

        if (!$bookingId) {
            return;
        }

        $booking = Booking::find($bookingId);

        if ($booking && $booking->status === 'pending' && !$booking->is_paid) {
            $booking->update([
                'status' => 'canceled',
            ]);

            Log::info("Booking #{$booking->code} canceled due to expired checkout session");
        }
    }

    /**
     * Handle payment_intent.succeeded event
     *
     * @param \Stripe\PaymentIntent $paymentIntent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;

        if (!$bookingId) {
            Log::warning('Payment intent succeeded but no booking_id in metadata: ' . $paymentIntent->id);
            return;
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            Log::warning('Booking not found for payment intent: ' . $paymentIntent->id);
            return;
        }

        // Update booking as paid if not already
        if (!$booking->is_paid) {
            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'paid_at' => now(),
                'payment_intent_id' => $paymentIntent->id,
            ]);

            Log::info("Booking #{$booking->code} marked as paid via webhook");
        }
    }

    /**
     * Handle payment_intent.payment_failed event
     *
     * @param \Stripe\PaymentIntent $paymentIntent
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;

        if (!$bookingId) {
            Log::warning('Payment intent failed but no booking_id in metadata: ' . $paymentIntent->id);
            return;
        }

        $booking = Booking::find($bookingId);

        if ($booking && $booking->status === 'pending') {
            $booking->update([
                'status' => 'payment_failed',
            ]);

            Log::info("Booking #{$booking->code} payment failed");
        }
    }

    /**
     * Handle payment_intent.canceled event
     *
     * @param \Stripe\PaymentIntent $paymentIntent
     */
    protected function handlePaymentIntentCanceled($paymentIntent)
    {
        $bookingId = $paymentIntent->metadata->booking_id ?? null;

        if (!$bookingId) {
            return;
        }

        $booking = Booking::find($bookingId);

        if ($booking && $booking->status === 'pending') {
            $booking->update([
                'status' => 'canceled',
            ]);

            Log::info("Booking #{$booking->code} canceled via webhook");
        }
    }

    /**
     * Handle charge.refunded event
     *
     * @param \Stripe\Charge $charge
     */
    protected function handleChargeRefunded($charge)
    {
        $paymentIntentId = $charge->payment_intent;

        if (!$paymentIntentId) {
            return;
        }

        $booking = Booking::where('payment_intent_id', $paymentIntentId)->first();

        if ($booking) {
            $booking->update([
                'status' => 'refunded',
                'is_paid' => false,
            ]);

            Log::info("Booking #{$booking->code} refunded via webhook");
        }
    }
}
