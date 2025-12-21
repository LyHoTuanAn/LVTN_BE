<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ShowtimeController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/api-docs', function () {
    return view('welcome');
})->name('api-docs');

// Route để serve các file tài liệu HTML từ doc/html/
Route::get('/api-docs/{filename}', function ($filename) {
    $filename = basename($filename);
    $filename = preg_replace('/\.html$/', '', $filename);
    $htmlPath = base_path("doc/html/{$filename}.html");
    
    if (File::exists($htmlPath)) {
        return response()->file($htmlPath, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
    
    abort(404, 'Documentation not found');
})->where('filename', '[a-zA-Z0-9_-]+');

// Route để serve file instructions.html từ doc/html/
Route::get('/docs/instructions', function () {
    $htmlPath = base_path('doc/html/instructions.html');
    
    if (File::exists($htmlPath)) {
        return response()->file($htmlPath, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
    
    abort(404, 'Instructions not found');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('web.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('web.logout');

// Protected dashboard routes
Route::middleware(['auth:web', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Admin Users Management
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/{id}/verify-email', [UserController::class, 'verifyEmail'])->name('verify-email');
    });

    // Admin Media Management
    Route::prefix('admin/media')->name('admin.media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/create-folder', [MediaController::class, 'createFolder'])->name('create-folder');
        Route::delete('/folder/{id}', [MediaController::class, 'deleteFolder'])->name('delete-folder');
        Route::post('/upload-file', [MediaController::class, 'uploadFile'])->name('upload-file');
        Route::put('/file/{id}/move', [MediaController::class, 'moveFile'])->name('move-file');
        Route::delete('/file/{id}', [MediaController::class, 'deleteFile'])->name('delete-file');
    });

    // Admin Movies Management
    Route::prefix('admin/movies')->name('admin.movies.')->group(function () {
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/', [MovieController::class, 'store'])->name('store');
        Route::get('/{id}', [MovieController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MovieController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/{id}', [MovieController::class, 'destroy'])->name('destroy');
    });

    // Admin Rooms Management
    Route::prefix('admin/rooms')->name('admin.rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');
    });

    // Admin Showtimes Management
    Route::prefix('admin/showtimes')->name('admin.showtimes.')->group(function () {
        Route::get('/', [ShowtimeController::class, 'index'])->name('index');
        Route::get('/create', [ShowtimeController::class, 'create'])->name('create');
        Route::post('/', [ShowtimeController::class, 'store'])->name('store');
        Route::get('/{id}', [ShowtimeController::class, 'show'])->name('show');
        Route::get('/{id}/seat-map', [ShowtimeController::class, 'seatMap'])->name('seat-map');
        Route::post('/{showtimeId}/seat/{seatId}/toggle-maintenance', [ShowtimeController::class, 'toggleSeatMaintenance'])->name('toggle-seat-maintenance');
        Route::get('/{id}/edit', [ShowtimeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ShowtimeController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShowtimeController::class, 'destroy'])->name('destroy');
    });

    // Admin Bookings Management
    Route::prefix('admin/bookings')->name('admin.bookings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/update-payment', [\App\Http\Controllers\Admin\BookingController::class, 'updatePayment'])->name('update-payment');
    });
});

// FinTech Dashboard Routes
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/api-management', function () {
    return view('api-management');
})->name('api-management');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');
