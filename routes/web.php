<?php

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
