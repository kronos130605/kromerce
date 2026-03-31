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
        $locale = $request->cookie('kromerce_locale')
            ?? session('locale')
            ?? config('app.locale', 'es');

        $supportedLocales = config('i18n.supported_locales', ['es' => 'Español']);
        if (!array_key_exists($locale, $supportedLocales)) {
            $locale = config('app.locale', 'es');
        }

        App::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
