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
            'current_tenant' => function () use ($request) {
                if (tenancy()->initialized) {
                    return tenant();
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
                    $tenant = tenancy()->initialized ? tenant() : null;

                    if (!$tenant) {
                        return 'customer';
                    }

                    // Usar RoleService con cache - ahora no hay redundancia real
                    return app(\App\Services\RoleService::class)
                        ->getUserRoleInTenant($user, $tenant);
                }

                // Para otras rutas, usar la lógica normal
                $tenant = tenancy()->initialized ? tenant() : null;

                if ($user && $tenant) {
                    return app(\App\Services\RoleService::class)
                        ->getUserRoleInTenant($user, $tenant);
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
