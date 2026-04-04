<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | List of all currencies supported by the application with their metadata.
    | This configuration is used throughout the application to avoid duplication.
    |
    */

    'supported' => [
        'USD' => [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'flag' => '🇺🇸',
        ],
        'EUR' => [
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'flag' => '🇪🇺',
        ],
        'GBP' => [
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'flag' => '🇬🇧',
        ],
        'CAD' => [
            'code' => 'CAD',
            'name' => 'Canadian Dollar',
            'symbol' => 'C$',
            'flag' => '🇨🇦',
        ],
        'MXN' => [
            'code' => 'MXN',
            'name' => 'Mexican Peso',
            'symbol' => '$',
            'flag' => '🇲🇽',
        ],
        'CUP' => [
            'code' => 'CUP',
            'name' => 'Peso Cubano',
            'symbol' => '$',
            'flag' => '🇨🇺',
        ],
        'MLC' => [
            'code' => 'MLC',
            'name' => 'Moneda Libremente Convertible',
            'symbol' => 'MLC',
            'flag' => '🇨🇺',
        ],
        'CLA' => [
            'code' => 'CLA',
            'name' => 'CLA (equiv. USD)',
            'symbol' => 'CLA',
            'flag' => '🇨🇺',
        ],
        'BRL' => [
            'code' => 'BRL',
            'name' => 'Brazilian Real',
            'symbol' => 'R$',
            'flag' => '🇧🇷',
        ],
        'ARS' => [
            'code' => 'ARS',
            'name' => 'Argentine Peso',
            'symbol' => '$',
            'flag' => '🇦🇷',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency code used when no currency is specified.
    |
    */

    'default' => env('DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Default Display Currencies
    |--------------------------------------------------------------------------
    |
    | The default currencies to display in multi-currency views.
    |
    */

    'default_display' => ['USD', 'EUR', 'CAD'],
];
