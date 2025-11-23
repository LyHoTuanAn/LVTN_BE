<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Try to get user from current guard (web or api)
        $user = Auth::user();

        if (!$user) {
            // For API requests, return JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHORIZED',
                    'message' => __('errors.UNAUTHORIZED'),
                ], 401);
            }

            // For web requests, redirect to login
            return redirect()->route('web.login.form');
        }

        $userRole = $user->role;

        if (!$userRole || !in_array($userRole->slug, $roles)) {
            // For API requests, return JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code' => 'FORBIDDEN',
                    'message' => __('errors.FORBIDDEN'),
                ], 403);
            }

            // For web requests, redirect to login with error message
            return redirect()->route('web.login.form')
                ->withErrors(['role' => __('errors.FORBIDDEN')]);
        }

        return $next($request);
    }
}


