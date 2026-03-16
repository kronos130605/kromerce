<?php

namespace App\Http\Middleware;

use App\Factories\RepositoryFactory;
use App\Repositories\User\UserStoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                $user = $request->user();
                if (!$user) {
                    return null;
                }

                // Cache store data for 30 minutes to avoid repeated queries
                $cacheKey = "user_current_store_{$user->id}";
                $storeData = cache()->remember($cacheKey, 1800, function () use ($user) {
                    // Get store ID directly from database
                    $storeId = DB::table('store_users')
                        ->where('user_id', $user->id)
                        ->where('is_active', true)
                        ->orderBy('joined_at', 'desc')
                        ->value('store_id');

                    if (!$storeId) {
                        return null;
                    }

                    // Use StoreService to get basic store data (optimized and cached)
                    $storeService = app(\App\Services\StoreService::class);
                    return $storeService->getBasicStoreDataForFrontend($storeId);
                });

                return $storeData;
            },
            'user_role' => function () use ($request) {
                $user = $request->user();
                if (!$user) {
                    return 'customer';
                }

                // Get current store from session
                $storeId = session('current_store_id');
                if (!$storeId) {
                    return 'customer';
                }

                // Use repository to get user's role in current store
                $userStoreRepository = RepositoryFactory::userStoreRepository();
                $userStore = $userStoreRepository->getUserRoleInStore($user->id, $storeId);

                // Return the role directly from DB, not the display name
                // available_roles is for display purposes, not for role mapping
                return $userStore ?? 'customer';
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
