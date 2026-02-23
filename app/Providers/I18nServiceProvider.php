<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
        $fallbackLocale = config('i18n.fallback_locale', 'en');
        
        // Load translations from JSON files
        $translations = [];
        $locales = config('i18n.supported_locales', ['en' => 'English']);
        
        foreach ($locales as $localeCode => $localeName) {
            $translationPath = config('i18n.translation_files.path', resource_path('js/locales')) . "/{$localeCode}/dashboard.json";
            
            if (File::exists($translationPath)) {
                $translations[$localeCode] = json_decode(File::get($translationPath), true);
            }
        }
        
        // Share with Inertia
        view()->share('translations', $translations);
        view()->share('currentLocale', $locale);
        view()->share('supportedLocales', $locales);
    }
}
