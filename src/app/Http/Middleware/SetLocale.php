<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set application locale from session or cookie.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('velntra_locale')
            ?? config('app.locale', 'en');

        if (in_array($locale, ['en', 'es'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
