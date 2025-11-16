<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header(config('api.header_name'));

        if (!$apiKey || $apiKey !== config('api.key')) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_API_KEY',
                'message' => __('errors.INVALID_API_KEY'),
            ], 401);
        }

        return $next($request);
    }
}


