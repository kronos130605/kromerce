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
        'JPY' => [
            'code' => 'JPY',
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'flag' => '🇯🇵',
        ],
        'COP' => [
            'code' => 'COP',
            'name' => 'Colombian Peso',
            'symbol' => '$',
            'flag' => '🇨🇴',
        ],
        'MXN' => [
            'code' => 'MXN',
            'name' => 'Mexican Peso',
            'symbol' => '$',
            'flag' => '🇲🇽',
        ],
        'CAD' => [
            'code' => 'CAD',
            'name' => 'Canadian Dollar',
            'symbol' => 'C$',
            'flag' => '🇨🇦',
        ],
        'AUD' => [
            'code' => 'AUD',
            'name' => 'Australian Dollar',
            'symbol' => 'A$',
            'flag' => '🇦🇺',
        ],
        'CHF' => [
            'code' => 'CHF',
            'name' => 'Swiss Franc',
            'symbol' => 'Fr',
            'flag' => '🇨🇭',
        ],
        'CNY' => [
            'code' => 'CNY',
            'name' => 'Chinese Yuan',
            'symbol' => '¥',
            'flag' => '🇨🇳',
        ],
        'INR' => [
            'code' => 'INR',
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'flag' => '🇮🇳',
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

    'default_display' => ['USD', 'EUR', 'COP'],
];
