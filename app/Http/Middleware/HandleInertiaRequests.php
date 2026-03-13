<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => function () use ($request) {
                    $user = $request->user();
                    if ($user) {
                        // Load roles with the user
                        $user->load('roles');
                        return $user;
                    }
                    return null;
                },
            ],
            'current_store' => function () use ($request) {
                if (tenancy()->initialized) {
                    return tenant(); // tenant() ahora retorna Store
                }
                return null;
            },
            'user_role' => function () use ($request) {
                $user = $request->user();

                // Usar la misma lógica que DashboardRoutingService para consistencia
                if (!$user) {
                    return 'customer';
                }

                // Para dashboard, usar la misma lógica que DashboardRoutingService
                if ($request->is('dashboard') || $request->is('business/dashboard')) {
                    $store = tenancy()->initialized ? tenant() : null; // tenant() ahora retorna Store

                    if (!$tenant) {
                        return 'customer';
                    }

                    // Usar RoleService con cache - ahora no hay redundancia real
                    return app(\App\Services\RoleService::class)
                        ->getUserRoleInStore($user, $store); // getUserRoleInStore() para stores
                }

                // Para otras rutas, usar la lógica normal
                $store = tenancy()->initialized ? tenant() : null; // tenant() ahora retorna Store

                if ($user && $tenant) {
                    return app(\App\Services\RoleService::class)
                        ->getUserRoleInStore($user, $store); // getUserRoleInStore() para stores
                }

                return 'customer';
            },
            'ziggy' => function () use ($request) {
                $ziggy = new \Tighten\Ziggy\Ziggy;
                return array_merge($ziggy->toArray(), [
                    'location' => $request->url(),
                    'query' => $request->query(),
                ]);
            },
        ];
    }
}
