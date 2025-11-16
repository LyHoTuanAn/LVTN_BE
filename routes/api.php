<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ShowtimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes must have middleware: api.key and language
| Order: ApiKeyMiddleware → LanguageMiddleware → JWT → Role → Permission
|
*/

// Public routes (no auth required)
Route::middleware(['api.key', 'language'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Public movie routes
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);

    // Public cinema routes
    Route::get('/cinemas', [CinemaController::class, 'index']);
    Route::get('/cinemas/{id}', [CinemaController::class, 'show']);

    // Public showtime routes
    Route::get('/showtimes', [ShowtimeController::class, 'index']);
    Route::get('/showtimes/{id}', [ShowtimeController::class, 'show']);
});

// Protected routes (require authentication)
Route::middleware(['api.key', 'language', 'auth:api'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Booking routes (customer)
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::delete('/{id}', [BookingController::class, 'cancel']);
    });
});


