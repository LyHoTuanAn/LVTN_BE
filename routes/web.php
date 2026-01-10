<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\ShowtimeController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\CinemaController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\NotificationWebController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admin\QrTicketController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Web\DashboardController;


Route::get('/api-docs', function () {
    return view('api-docs');
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

// Language switch route
Route::get('/language/{locale}', [AuthController::class, 'switchLanguage'])->name('language.switch');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('web.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('web.logout');

// Protected dashboard routes
Route::middleware(['auth:web', 'role:admin'])->group(function () {
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

    // Admin Cinemas Management
    Route::prefix('admin/cinemas')->name('admin.cinemas.')->group(function () {
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/', [CinemaController::class, 'store'])->name('store');
        Route::get('/create-many', [CinemaController::class, 'createMany'])->name('create-many');
        Route::post('/store-many', [CinemaController::class, 'storeMany'])->name('store-many');
        Route::get('/{id}', [CinemaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/{id}', [CinemaController::class, 'destroy'])->name('destroy');
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

    // Admin Room Types Management
    Route::prefix('admin/room-types')->name('admin.room-types.')->group(function () {
        Route::get('/', [RoomTypeController::class, 'index'])->name('index');
        Route::get('/create', [RoomTypeController::class, 'create'])->name('create');
        Route::post('/', [RoomTypeController::class, 'store'])->name('store');
        Route::get('/{roomType}', [RoomTypeController::class, 'show'])->name('show');
        Route::get('/{roomType}/edit', [RoomTypeController::class, 'edit'])->name('edit');
        Route::put('/{roomType}', [RoomTypeController::class, 'update'])->name('update');
        Route::delete('/{roomType}', [RoomTypeController::class, 'destroy'])->name('destroy');
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

    // Admin News Management
    Route::prefix('admin/news')->name('admin.news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::post('/translate', [NewsController::class, 'translate'])->name('translate');
        Route::get('/{id}', [NewsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{id}', [NewsController::class, 'destroy'])->name('destroy');
    });

    // Admin Bookings Management
    Route::prefix('admin/bookings')->name('admin.bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [BookingController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/update-payment', [BookingController::class, 'updatePayment'])->name('update-payment');
    });

    // Admin QR Ticket Scanner
    Route::prefix('admin/qr-ticket')->name('admin.qr-ticket.')->group(function () {
        Route::get('/', [QrTicketController::class, 'index'])->name('index');
        Route::post('/scan', [QrTicketController::class, 'scan'])->name('scan');
        Route::post('/lookup', [QrTicketController::class, 'lookup'])->name('lookup');
    });

    // Admin Vouchers Management
    Route::prefix('admin/vouchers')->name('admin.vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/create', [VoucherController::class, 'create'])->name('create');
        Route::post('/', [VoucherController::class, 'store'])->name('store');
        Route::get('/{id}', [VoucherController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VoucherController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VoucherController::class, 'update'])->name('update');
        Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('destroy');
    });

    // Admin Reviews Management
    Route::prefix('admin/reviews')->name('admin.reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [ReviewController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [ReviewController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // Admin Notifications Management
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [NotificationWebController::class, 'index'])->name('index');
        Route::post('/send-to-user', [NotificationWebController::class, 'sendToUser'])->name('send-to-user');
        Route::post('/send-to-all', [NotificationWebController::class, 'sendToAll'])->name('send-to-all');
        Route::post('/send-to-topic', [NotificationWebController::class, 'sendToTopic'])->name('send-to-topic');
    });
});

// FinTech Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/api-management', function () {
    return view('api-management');
})->name('api-management');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');
