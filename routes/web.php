<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return view('welcome');
});

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
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('web.login.form');
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
});

Route::middleware(['auth:web', 'role:customer'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});

Route::middleware(['auth:web', 'role:partner'])->group(function () {
    Route::get('/partner/dashboard', function () {
        return view('partner.dashboard');
    })->name('partner.dashboard');
});
