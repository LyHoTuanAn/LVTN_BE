<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
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

        if (!$userRole) {
            return response()->json([
                'success' => false,
                'code' => 'FORBIDDEN',
                'message' => __('errors.FORBIDDEN'),
            ], 403);
        }

        $userPermissions = $userRole->permissions()->pluck('slug')->toArray();

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return response()->json([
                    'success' => false,
                    'code' => 'FORBIDDEN',
                    'message' => __('errors.FORBIDDEN'),
                ], 403);
            }
        }

        return $next($request);
    }
}


