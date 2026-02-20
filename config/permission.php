<?php

return [

    'models' => [
        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */
        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */
        'role' => Spatie\Permission\Models\Role::class,
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */
        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to name the roles and permissions pivot table other than
         * `model_has_permissions`. You can also optionally set a custom pivot model.
         */
        'model_has_permissions' => 'model_has_permissions',

        /*
         * Change this if you want to name the roles and permissions pivot table other than
         * `model_has_roles`. You can also optionally set a custom pivot model.
         */
        'model_has_roles' => 'model_has_roles',

        /*
         * Change this if you want to name the permissions and roles pivot table other than
         * `role_has_permissions`. You can also optionally set a custom pivot model.
         */
        'role_has_permissions' => 'role_has_permissions',
    ],

    /*
     * When set to true, the required permission names are added to the exception
     * message. This could be an issue in broader production usage, where you
     * don't want to leak your permission names to your users.
     */
    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to the exception
     * message. This could be an issue in broader production usage, where you
     * don't want to leak your role names to your users.
     */
    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are enabled.
     */
    'enable_wildcard_permission' => false,

    /*
     * By default all permissions will be cached for 24 hours.
     *
     * You can set this value to `null` to disable the cache entirely.
     */
    'cache' => [
        'expiration_time' => 86400, // 24 hours in seconds

        /*
         * The cache key used to store all permissions.
         */
        'key' => 'spatie.permission.cache',

        /*
         * When checking for a permission against a model, you may want to cache
         * certain checks. This will store the permission check in the cache for
         * the specified time.
         */
        'model_check' => [
            'expiration_time' => 18000, // 5 hours in seconds
            'key' => 'spatie.permission.model.cache',
        ],

        /*
         * The cache tag that will be used when caching permissions and roles.
         */
        'tags' => ['spatie.permission.cache'],
    ],
];
