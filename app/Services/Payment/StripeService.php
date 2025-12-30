<?php

namespace App\Services\Payment;

use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\ApiErrorException;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for a booking
     * Returns a checkout URL that can be opened directly in mobile WebView
     *
     * @param Booking $booking
     * @param array $metadata Additional metadata
     * @return CheckoutSession
     * @throws Exception
     */
    public function createCheckoutSession(Booking $booking, array $metadata = []): CheckoutSession
    {
        try {
            // Convert VND to smallest unit (already in VND, no decimal places)
            $amount = (int) $booking->total_price;

            // Validate minimum amount
            if ($amount < config('stripe.minimum_amount', 10000)) {
                throw new Exception(__('errors.STRIPE_AMOUNT_TOO_SMALL'));
            }

            // Build line items for checkout
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => config('stripe.currency', 'vnd'),
                        'product_data' => [
                            'name' => $metadata['movie_title'] ?? 'Movie Ticket',
                            'description' => "Booking Code: {$booking->code}",
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ],
            ];

            // Create checkout session
            $sessionData = [
                'payment_method_types' => config('stripe.payment_methods', ['card']),
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => config('stripe.success_url', config('app.url') . '/payment/success') . '?booking_code=' . $booking->code . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('stripe.cancel_url', config('app.url') . '/payment/cancel') . '?booking_code=' . $booking->code,
                'metadata' => array_merge([
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->code,
                    'user_id' => $booking->user_id,
                ], $metadata),
                'expires_at' => time() + (30 * 60), // Session expires in 30 minutes
            ];

            // Add customer email if available
            if ($booking->user && $booking->user->email) {
                $sessionData['customer_email'] = $booking->user->email;
            }

            return CheckoutSession::create($sessionData);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_CHECKOUT_SESSION_FAILED') . ': ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a Checkout Session by ID
     *
     * @param string $sessionId
     * @return CheckoutSession
     * @throws Exception
     */
    public function getCheckoutSession(string $sessionId): CheckoutSession
    {
        try {
            return CheckoutSession::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_CHECKOUT_SESSION_NOT_FOUND'));
        }
    }

    /**
     * Check if checkout session payment is completed
     *
     * @param string $sessionId
     * @return bool
     */
    public function isCheckoutSessionPaid(string $sessionId): bool
    {
        try {
            $session = $this->getCheckoutSession($sessionId);
            return $session->payment_status === 'paid';
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create a payment intent for a booking
     *
     * @param Booking $booking
     * @param array $metadata Additional metadata to attach to payment
     * @return PaymentIntent
     * @throws Exception
     */
    public function createPaymentIntent(Booking $booking, array $metadata = []): PaymentIntent
    {
        try {
            // Convert VND to smallest unit (already in VND, no decimal places)
            $amount = (int) $booking->total_price;

            // Validate minimum amount
            if ($amount < config('stripe.minimum_amount', 10000)) {
                throw new Exception(__('errors.STRIPE_AMOUNT_TOO_SMALL'));
            }

            $paymentIntentData = [
                'amount' => $amount,
                'currency' => config('stripe.currency', 'vnd'),
                'payment_method_types' => config('stripe.payment_methods', ['card']),
                'capture_method' => config('stripe.capture_method', 'automatic'),
                'metadata' => array_merge([
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->code,
                    'user_id' => $booking->user_id,
                ], $metadata),
                'description' => "Payment for booking #{$booking->code}",
            ];

            // Add user email if available
            if ($booking->user && $booking->user->email) {
                $paymentIntentData['receipt_email'] = $booking->user->email;
            }

            return PaymentIntent::create($paymentIntentData);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_PAYMENT_INTENT_FAILED') . ': ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a payment intent by ID
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws Exception
     */
    public function getPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_PAYMENT_INTENT_NOT_FOUND'));
        }
    }

    /**
     * Confirm a payment intent
     *
     * @param string $paymentIntentId
     * @param string $paymentMethodId
     * @return PaymentIntent
     * @throws Exception
     */
    public function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId)->confirm([
                'payment_method' => $paymentMethodId,
            ]);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_PAYMENT_CONFIRM_FAILED') . ': ' . $e->getMessage());
        }
    }

    /**
     * Cancel a payment intent
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws Exception
     */
    public function cancelPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId)->cancel();
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_PAYMENT_CANCEL_FAILED'));
        }
    }

    /**
     * Create a refund for a payment intent
     *
     * @param string $paymentIntentId
     * @param int|null $amount Amount to refund (null for full refund)
     * @param string $reason Reason for refund
     * @return Refund
     * @throws Exception
     */
    public function createRefund(string $paymentIntentId, ?int $amount = null, string $reason = 'requested_by_customer'): Refund
    {
        try {
            $refundData = [
                'payment_intent' => $paymentIntentId,
                'reason' => $reason,
            ];

            if ($amount !== null) {
                $refundData['amount'] = $amount;
            }

            return Refund::create($refundData);
        } catch (ApiErrorException $e) {
            throw new Exception(__('errors.STRIPE_REFUND_FAILED') . ': ' . $e->getMessage());
        }
    }

    /**
     * Verify and construct webhook event
     *
     * @param string $payload
     * @param string $signature
     * @return \Stripe\Event
     * @throws Exception
     */
    public function constructWebhookEvent(string $payload, string $signature): \Stripe\Event
    {
        $webhookSecret = config('stripe.webhook_secret');

        if (empty($webhookSecret)) {
            throw new Exception('Stripe webhook secret is not configured');
        }

        try {
            return Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            throw new Exception(__('errors.STRIPE_WEBHOOK_SIGNATURE_INVALID'));
        }
    }

    /**
     * Check if payment intent is successful
     *
     * @param string $paymentIntentId
     * @return bool
     */
    public function isPaymentSuccessful(string $paymentIntentId): bool
    {
        try {
            $paymentIntent = $this->getPaymentIntent($paymentIntentId);
            return $paymentIntent->status === 'succeeded';
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get payment intent status
     *
     * @param string $paymentIntentId
     * @return string|null
     */
    public function getPaymentStatus(string $paymentIntentId): ?string
    {
        try {
            $paymentIntent = $this->getPaymentIntent($paymentIntentId);
            return $paymentIntent->status;
        } catch (Exception $e) {
            return null;
        }
    }
}
