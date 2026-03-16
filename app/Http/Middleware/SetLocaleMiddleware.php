<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');
        
        // Handle potential list of languages in Accept-Language header (e.g., 'en-US,en;q=0.9')
        if ($locale) {
            $locale = explode(',', $locale)[0];
            $locale = explode(';', $locale)[0];
            $locale = trim(strtolower($locale));
            // Keep only the first part if it's 'en-US' -> 'en'
            $locale = explode('-', $locale)[0];
        }

        if ($locale && in_array($locale, config('languages.codes', ['ar', 'en']))) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('languages.default', 'ar'));
        }

        return $next($request);
    }
}
