<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for locale in cookie (set by frontend)
        if ($request->hasCookie('kromerce_locale')) {
            $locale = $request->cookie('kromerce_locale');
            if (in_array($locale, ['es', 'en'])) {
                app()->setLocale($locale);
                session(['locale' => $locale]);
            }
        }
        // Fallback to session locale
        elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
