<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class I18nServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Share translations with frontend
        $this->shareTranslations();
    }

    /**
     * Share translations with frontend.
     */
    protected function shareTranslations()
    {
        $locale = app()->getLocale();

        // Load translations from JSON files in lang/{locale}/
        $translations = [];
        $locales = config('i18n.supported_locales', ['es' => 'Español', 'en' => 'English']);
        $langPath = base_path('lang');

        foreach ($locales as $localeCode => $localeName) {
            $localePath = "{$langPath}/{$localeCode}";
            
            if (File::isDirectory($localePath)) {
                $files = File::files($localePath, '*.json');
                $localeTranslations = [];
                
                foreach ($files as $file) {
                    $namespace = $file->getFilenameWithoutExtension();
                    $content = json_decode(File::get($file->getPathname()), true);
                    if ($content !== null) {
                        $localeTranslations[$namespace] = $content;
                    }
                }
                
                if (!empty($localeTranslations)) {
                    $translations[$localeCode] = $localeTranslations;
                }
            }
        }

        // Log para depuración
        Log::debug('I18nServiceProvider sharing translations', [
            'current_locale' => $locale,
            'available_locales' => array_keys($translations),
            'has_products_es' => isset($translations['es']['products']),
            'has_products_en' => isset($translations['en']['products']),
        ]);

        // Share with Inertia
        Inertia::share('translations', $translations);
        Inertia::share('currentLocale', $locale);
        Inertia::share('supportedLocales', $locales);
    }
}
