<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Leer locale de cookie, header (para Inertia), o sesión
        $locale = $request->cookie('kromerce_locale')
            ?? $request->header('X-Locale')
            ?? session('locale')
            ?? config('app.locale', 'es');

        $supportedLocales = config('i18n.supported_locales', ['es' => 'Español']);
        if (!array_key_exists($locale, $supportedLocales)) {
            $locale = config('app.locale', 'es');
        }

        App::setLocale($locale);
        session(['locale' => $locale]);

        // Log para depuración (eliminar en producción)
        Log::debug('SetLocale middleware', [
            'cookie_locale' => $request->cookie('kromerce_locale'),
            'header_locale' => $request->header('X-Locale'),
            'session_locale' => session('locale'),
            'final_locale' => $locale,
            'cookies_all' => $request->cookies->all(),
        ]);

        return $next($request);
    }
}
