<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | This option determines the default locale that will be used by the
    | translation service. The locale specified here will be used as the
    | fallback locale when the current one is not available.
    |
    */
    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders in your resources/lang directory.
    |
    */
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | This array contains all the locales that are supported by the application.
    | This is used by the translation service to determine which locales
    | are available.
    |
    */
    'supported_locales' => [
        'en' => 'English',
        'es' => 'Español',
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Files Path
    |--------------------------------------------------------------------------
    |
    | This option determines the path where the translation files are stored.
    | By default, Laravel will look for language files in the resources/lang directory.
    | For Vue.js frontend, we'll use the resources/js/locales directory.
    |
    */
    'translation_files' => [
        'path' => resource_path('js/locales'),
        'json' => true,
    ],
];
