<?php

namespace App\Http\Middleware;

use App\Services\CountryContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCountryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to get country ID from header 'x-country-id'
        $countryId = $request->header('x-country-id');
        
        if ($countryId) {
            app(CountryContext::class)->setCountryId((int) $countryId);
        }

        return $next($request);
    }
}
