<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => __('errors.UNAUTHORIZED'),
            ], 401);
        }

        $userRole = $user->role;

        if (!$userRole || !in_array($userRole->slug, $roles)) {
            return response()->json([
                'success' => false,
                'code' => 'FORBIDDEN',
                'message' => __('errors.FORBIDDEN'),
            ], 403);
        }

        return $next($request);
    }
}


