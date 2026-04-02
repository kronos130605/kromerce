<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on first page visit.
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
        $shared = parent::share($request);

        $shared['auth'] = [
            'user' => $request->user()?->load('roles'),
        ];

        try {
            $storeService = app(\App\Services\StoreService::class);
            $store = $storeService->resolveCurrentStoreForRequest($request);
            $shared['current_store'] = $store ? $storeService->getBasicStoreDataForFrontend($store->id) : null;
        } catch (\Throwable $e) {
            logger('[HandleInertiaRequests] ERROR resolving current_store: ' . $e->getMessage());
            $shared['current_store'] = null;
        }

        $shared['ziggy'] = fn () => array_merge((new \Tighten\Ziggy\Ziggy)->toArray(), [
            'location' => $request->url(),
        ]);

        return $shared;
    }
}
