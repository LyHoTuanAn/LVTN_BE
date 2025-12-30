<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The publishable key is used in frontend to create payment
    | intents, while the secret key is used in backend to manage payments.
    |
    */
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. If not specified, defaults to 'vnd'.
    |
    */
    'currency' => env('STRIPE_CURRENCY', 'vnd'),

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | These settings control how payments are handled in your application.
    |
    */

    // Minimum amount in VND (Stripe requires minimum amount)
    'minimum_amount' => 10000, // 10,000 VND

    // Automatic capture vs manual capture
    'capture_method' => 'automatic', // 'automatic' or 'manual'

    // Payment methods enabled
    'payment_methods' => ['card'],

    /*
    |--------------------------------------------------------------------------
    | Checkout Session URLs
    |--------------------------------------------------------------------------
    |
    | These URLs are used by Stripe Checkout to redirect users after payment.
    | For mobile apps, these should be deep links (e.g., myapp://payment/success)
    |
    */
    'success_url' => env('STRIPE_SUCCESS_URL', 'myapp://payment/success'),
    'cancel_url' => env('STRIPE_CANCEL_URL', 'myapp://payment/cancel'),
];
