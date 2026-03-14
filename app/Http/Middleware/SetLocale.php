<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

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
        // Get locale from request header, session, or cookie
        $locale = $request->header('Accept-Language')
            ?? session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'es');

        // Validate locale is supported
        $supportedLocales = config('i18n.supported_locales', ['es' => 'Español']);
        if (!array_key_exists($locale, $supportedLocales)) {
            $locale = config('app.locale', 'es');
        }

        // Set locale for the application
        App::setLocale($locale);

        // Set locale in session
        session(['locale' => $locale]);

        // Load translations for frontend - ONLY current locale to avoid memory issues
        $translations = [];
        $translationPath = config('i18n.translation_files.path', resource_path('js/i18n/locales')) . "/{$locale}.json";

        if (File::exists($translationPath)) {
            $translations = json_decode(File::get($translationPath), true);
        }

        // Share translations with Inertia - only current locale
        Inertia::share('translations', $translations);
        Inertia::share('currentLocale', $locale);
        Inertia::share('supportedLocales', $supportedLocales);

        return $next($request);
    }
}
