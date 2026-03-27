<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Settings Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all default settings configurations for stores.
    | Centralizing settings makes it easier to maintain and modify default
    | values across the application without duplicating arrays in multiple places.
    |
    */

    /**
     * Base settings that apply to all stores
     * These are the fundamental settings every store should have
     */
    'base_settings' => [
        'theme' => 'default',
        'primary_color' => '#3B82F6',
        'secondary_color' => '#10B981',
        'accent_color' => '#F59E0B',
        'default_currency' => 'USD',
        'language' => 'es',
        'timezone' => 'America/Mexico_City',
        'enable_notifications' => true,
        'created_by_system' => true,
    ],

    /**
     * Store type specific settings
     * These settings are applied based on the store type/slug
     */
    'store_type_settings' => [
        'customers-default' => [
            'show_flash_sales' => true,
            'show_featured_stores' => true,
            'show_ai_recommendations' => true,
            'enable_wishlist' => true,
            'enable_reviews' => true,
            'layout' => [
                'sidebar_position' => 'left',
                'product_grid_columns' => 4,
                'show_product_ratings' => true,
                'show_product_compare' => true,
            ],
        ],
        'business-default' => [
            'show_analytics' => true,
            'show_inventory_management' => true,
            'show_order_management' => true,
            'show_customer_management' => true,
            'show_financial_reports' => true,
            'enable_multi_currency' => true,
            'layout' => [
                'sidebar_position' => 'left',
                'default_dashboard_view' => 'overview',
                'show_quick_actions' => true,
                'show_recent_activity' => true,
            ],
        ],
    ],

    /**
     * Theme configuration
     * Available themes and their configurations
     */
    'themes' => [
        'default' => [
            'name' => 'Default Theme',
            'description' => 'Clean and modern default theme',
            'colors' => [
                'primary' => '#3B82F6',
                'secondary' => '#10B981',
                'accent' => '#F59E0B',
                'success' => '#22C55E',
                'warning' => '#F59E0B',
                'error' => '#EF4444',
                'info' => '#3B82F6',
            ],
        ],
        'dark' => [
            'name' => 'Dark Theme',
            'description' => 'Dark mode theme for low-light environments',
            'colors' => [
                'primary' => '#60A5FA',
                'secondary' => '#34D399',
                'accent' => '#FBBF24',
                'success' => '#4ADE80',
                'warning' => '#FBBF24',
                'error' => '#F87171',
                'info' => '#60A5FA',
            ],
        ],
        'minimal' => [
            'name' => 'Minimal Theme',
            'description' => 'Clean and minimal design with focus on content',
            'colors' => [
                'primary' => '#6B7280',
                'secondary' => '#9CA3AF',
                'accent' => '#F59E0B',
                'success' => '#22C55E',
                'warning' => '#F59E0B',
                'error' => '#EF4444',
                'info' => '#6B7280',
            ],
        ],
    ],

    /**
     * Currency configuration
     * Supported currencies and their settings
     */
    'currencies' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'precision' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'precision' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ],
        'MXN' => [
            'name' => 'Mexican Peso',
            'symbol' => '$',
            'code' => 'MXN',
            'precision' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'code' => 'GBP',
            'precision' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ],
    ],

    /**
     * Supported languages
     * Available languages for the application
     */
    'languages' => [
        'es' => [
            'name' => 'Español',
            'native_name' => 'Español',
            'code' => 'es',
            'locale' => 'es_MX',
            'flag' => '🇲🇽',
        ],
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'locale' => 'en_US',
            'flag' => '🇺🇸',
        ],
    ],

    /**
     * Common timezones
     * Frequently used timezones for store configuration
     */
    'timezones' => [
        'America/Mexico_City' => 'Mexico City',
        'America/New_York' => 'New York',
        'America/Los_Angeles' => 'Los Angeles',
        'America/Chicago' => 'Chicago',
        'Europe/London' => 'London',
        'Europe/Madrid' => 'Madrid',
        'Asia/Tokyo' => 'Tokyo',
        'Australia/Sydney' => 'Sydney',
    ],

    /**
     * Layout defaults
     * Default layout configurations for different store types
     */
    'layout_defaults' => [
        'customer' => [
            'sidebar_position' => 'left',
            'product_grid_columns' => 4,
            'show_product_ratings' => true,
            'show_product_compare' => true,
            'enable_filters' => true,
            'enable_search' => true,
        ],
        'business' => [
            'sidebar_position' => 'left',
            'default_dashboard_view' => 'overview',
            'show_quick_actions' => true,
            'show_recent_activity' => true,
            'compact_mode' => false,
        ],
    ],

    /**
     * Feature flags
     * Enable/disable features for different store types
     */
    'features' => [
        'customer' => [
            'flash_sales' => true,
            'featured_stores' => true,
            'ai_recommendations' => true,
            'wishlist' => true,
            'reviews' => true,
            'product_compare' => true,
            'social_login' => true,
        ],
        'business' => [
            'analytics' => true,
            'inventory_management' => true,
            'order_management' => true,
            'customer_management' => true,
            'financial_reports' => true,
            'multi_currency' => true,
            'api_access' => true,
            'bulk_operations' => true,
        ],
    ],
];
