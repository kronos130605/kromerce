<?php

return [
    'presets' => [
        'products' => ['products', 'common', 'errors', 'dashboard_nav'],
        'dashboard' => ['dashboard', 'common', 'business', 'products', 'orders', 'errors', 'currency', 'dashboard_nav'],
        'auth' => ['auth', 'common', 'errors'],
        'register' => ['auth', 'common', 'errors'],
        'orders' => ['orders', 'dashboard_nav', 'common', 'errors'],
        'analytics' => ['dashboard', 'common', 'business', 'errors', 'dashboard_nav'],
        'storefront' => ['storefront', 'common', 'errors', 'products', 'currency'],
        'profile' => ['common', 'auth', 'errors', 'dashboard_nav'],
        'welcome' => ['welcome', 'common', 'auth'],
        'settings' => ['settings', 'common', 'errors', 'dashboard_nav'],
    ],
];
