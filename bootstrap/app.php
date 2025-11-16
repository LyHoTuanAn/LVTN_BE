<?php

use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register API middleware
        $middleware->alias([
            'api.key' => ApiKeyMiddleware::class,
            'language' => LanguageMiddleware::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle validation errors for API routes
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                $errors = [];
                foreach ($e->errors() as $field => $messages) {
                    $errors[$field] = is_array($messages) ? $messages[0] : $messages;
                }

                return ApiResponse::error(
                    'VALIDATION_ERROR',
                    $errors,
                    __('errors.VALIDATION_ERROR'),
                    422
                );
            }
        });
    })->create();
