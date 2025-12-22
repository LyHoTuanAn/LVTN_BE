<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For API: get from header
        // For Web: get from session, fallback to header, then default to 'en'
        if ($request->is('api/*')) {
            $language = $request->header('Language', 'en');
        } else {
            // Web routes: check session first, then header, then default
            $language = $request->session()->get('locale', $request->header('Language', 'en'));
        }

        // Chỉ chấp nhận 'en' hoặc 'vi'
        if (!in_array($language, ['en', 'vi'])) {
            $language = 'en';
        }

        App::setLocale($language);

        return $next($request);
    }
}


