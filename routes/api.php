<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ShowtimeController;
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
});

// Protected routes (require authentication)
Route::middleware(['language', 'api.key', 'auth:api'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Booking routes (customer)
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->where('id', '[0-9]+');
    });

    // Media routes
    Route::prefix('media')->group(function () {
        Route::post('/upload-image', [MediaController::class, 'uploadImage']);
    });
});


