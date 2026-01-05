<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\FavoriteMovieController;
use App\Http\Controllers\Api\VNPayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes must have middleware: language and api.key
| Order: LanguageMiddleware → ApiKeyMiddleware → JWT → Role → Permission
|
*/

// Stripe Webhook (no auth required, no API key required)
// Must be before the middleware group
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// VNPay Callbacks (no auth required, no API key required)
// IPN: VNPay server-to-server notification
Route::get('/vnpay/ipn', [VNPayController::class, 'ipn']);
// Return URL: Redirect user after payment
Route::get('/vnpay/return', [VNPayController::class, 'returnUrl']);

// Public routes (no auth required)
Route::middleware(['language', 'api.key'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/refresh', [AuthController::class, 'refresh']); // Public route - uses refresh_token from body
    });

    // Public movie routes
    Route::get('/movies/search', [MovieController::class, 'search']);
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::get('/movies/{id}/showtimes', [MovieController::class, 'showtimes']);

    // Public cinema routes
    Route::get('/cinemas', [CinemaController::class, 'index']);
    Route::get('/cinemas/{id}', [CinemaController::class, 'show']);

    // Public showtime routes
    Route::get('/showtimes', [ShowtimeController::class, 'index']);
    Route::get('/showtimes/{id}', [ShowtimeController::class, 'show']);
    Route::get('/showtimes/{id}/seats', [ShowtimeController::class, 'seats']);

    // Home route
    Route::get('/home', [HomeController::class, 'index']);

    // Stripe publishable key (public, no auth needed)
    Route::get('/payments/stripe-key', [PaymentController::class, 'getPublishableKey']);
});

// Protected routes (require authentication)
Route::middleware(['language', 'api.key', 'auth:api'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/fcm-token', [AuthController::class, 'fcmToken']);
    });

    // Booking routes (customer)
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->where('id', '[0-9]+');
    });

    // Payment routes (Stripe)
    // Note: create-intent is now integrated into POST /api/bookings
    Route::prefix('payments')->group(function () {
        Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
        Route::get('/status/{booking_id}', [PaymentController::class, 'getPaymentStatus'])->where('booking_id', '[0-9]+');
        Route::post('/cancel', [PaymentController::class, 'cancelPayment']);
    });

    // Media routes
    Route::prefix('media')->group(function () {
        Route::post('/upload-image', [MediaController::class, 'uploadImage']);
    });

    // Favorite movies routes
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteMovieController::class, 'index']);
        Route::post('/{movieId}', [FavoriteMovieController::class, 'store'])->where('movieId', '[0-9]+');
        Route::delete('/{movieId}', [FavoriteMovieController::class, 'destroy'])->where('movieId', '[0-9]+');
    });

    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/{id}', [ReviewController::class, 'show'])->where('id', '[0-9]+');
    });

    // User Notification routes
    // GET /api/notifications - Returns notifications with unread_count, auto marks all as read
    Route::get('/notifications', [NotificationController::class, 'index']);

    // Admin Cinema routes (require admin role)
    Route::middleware(['role:admin'])->prefix('admin/cinemas')->group(function () {
        Route::post('/bulk', [\App\Http\Controllers\Admin\CinemaController::class, 'apiStoreMany']);
    });

    // Admin Notification routes (require admin role)
    Route::middleware(['role:admin'])->prefix('admin/notifications')->group(function () {
        Route::get('/topics', [\App\Http\Controllers\Admin\NotificationController::class, 'getTopics']);
        Route::post('/send-to-user', [\App\Http\Controllers\Admin\NotificationController::class, 'sendToUser']);
        Route::post('/send-to-users', [\App\Http\Controllers\Admin\NotificationController::class, 'sendToUsers']);
        Route::post('/send-to-all', [\App\Http\Controllers\Admin\NotificationController::class, 'sendToAllUsers']);
        Route::post('/send-to-topic', [\App\Http\Controllers\Admin\NotificationController::class, 'sendToTopic']);
    });
});

