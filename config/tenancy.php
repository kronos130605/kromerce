<?php

return [
    'tenant_model' => \App\Models\Tenant::class,

    'central_domains' => [
        'kromerce.test',
        'localhost',
        '127.0.0.1',
    ],

    'database' => [
        'prefix' => 'tenant',
        'suffix' => '',
    ],

    'redis' => [
        'prefix' => 'tenant',
        'suffix' => '',
    ],

    'cache' => [
        'prefix' => 'tenant',
        'suffix' => '',
    ],

    'filesystem' => [
        'suffix_base' => '_tenant',
        'disks' => [
            'local',
            'public',
            's3',
        ],
    ],

    'bootstrappers' => [
        'Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper',
        'Stancl\Tenancy\Bootstrappers\RedisTenancyBootstrapper',
        'Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper',
        'Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper',
    ],

    'features' => [
        // 'Stancl\Tenancy\Features\UniversalRoutes',
        // 'Stancl\Tenancy\Features\TenantConfig',
        // 'Stancl\Tenancy\Features\TenantRedirect',
        // 'Stancl\Tenancy\Features\TelescopeTags',
    ],
];
