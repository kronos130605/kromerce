<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
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
        // Get locale from cookie (set by frontend), session, or default
        // TODO: Remove hardcoded 'es' once English translations are ready
        $locale = $request->cookie('kromerce_locale')
            ?? session('locale')
            ?? config('app.locale', 'es');
        
        // Temporary: Force Spanish until we have English translations
        $locale = 'es';

        // Validate locale is supported
        $supportedLocales = config('i18n.supported_locales', ['es' => 'Español']);
        if (!array_key_exists($locale, $supportedLocales)) {
            $locale = config('app.locale', 'es');
        }

        // Set locale for the application
        App::setLocale($locale);

        // Set locale in session
        session(['locale' => $locale]);

        // Note: Specific translations are now loaded by controllers using TranslationHelper
        // This middleware only sets the locale, actual translations come via Inertia props per view

        return $next($request);
    }
}
