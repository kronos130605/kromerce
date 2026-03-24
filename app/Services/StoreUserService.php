<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use App\Repositories\Store\StoreRepository;
use App\Repositories\User\UserStoreRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class StoreUserService
{
    public function __construct(
        private UserStoreRepository $userStoreRepository,
        private StoreRepository $storeRepository
    ) {}

    public function getUserCurrentStore(User $user): ?Store
    {
        return $this->userStoreRepository->getUserCurrentStore($user);
    }

    public function setUserCurrentStore(User $user, Store $store): bool
    {
        return $this->userStoreRepository->setUserCurrentStore($user, $store);
    }

    public function getUserStores(User $user): Collection
    {
        return $this->userStoreRepository->getUserStores($user);
    }

    public function assignStoreToUser(User $user, Store $store): bool
    {
        try {
            return $this->userStoreRepository->attachUserToStore($user, $store);
        } catch (\Exception $e) {
            Log::error('Error assigning store to user', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getOrCreateDefaultStoreForUser(User $user): ?Store
    {
        try {
            $store = $this->userStoreRepository->getUserFirstStore($user);

            if (!$store) {
                $userRoles = $user->roles->pluck('name')->toArray();
                $defaultSlug = $this->getDefaultStoreSlugForRoles($userRoles);
                $store = $this->storeRepository->getFirstBy(['slug' => $defaultSlug]);

                if (!$store) {
                    $store = $this->createDefaultStore($defaultSlug, $userRoles, $user);
                } else {
                    $this->userStoreRepository->attachUserToStore($user, $store);
                }
            }

            return $store;
        } catch (\Exception $e) {
            Log::error('Error getting or creating default store for user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function createDefaultStore(string $slug, array $userRoles, ?User $user = null): ?Store
    {
        try {
            $settings = $this->getDefaultStoreSettings($slug, $userRoles);
            $ownerId = $user?->id ?? $this->storeRepository->getAvailableOwnerId();

            if (!$ownerId) {
                throw new \Exception('No available user found for owner_id');
            }

            $storeId = $this->storeRepository->createDirect([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $this->getDefaultStoreName($slug),
                'slug' => $slug,
                'data' => json_encode($settings),
                'status' => 'active',
                'owner_id' => $ownerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $store = $this->storeRepository->getFirstBy(['id' => $storeId]);

            if (!$store) {
                throw new \Exception('Failed to retrieve created store');
            }

            return $store;
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), $slug)) {
                Log::warning('Store with slug already exists, retrieving existing store', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);

                $existingStore = $this->storeRepository->getFirstBy(['slug' => $slug]);
                if ($existingStore && $user) {
                    $this->userStoreRepository->attachUserToStore($user, $existingStore);
                    return $existingStore;
                }
            }

            Log::error('Error creating default store', [
                'slug' => $slug,
                'user_roles' => $userRoles,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    public function getDefaultStoreSlugForRoles(array $userRoles): string
    {
        $businessRoles = config('roles.business_roles', ['business_owner']);

        foreach ($businessRoles as $role) {
            if (in_array($role, $userRoles)) {
                return config('roles.default_store_slugs.business', 'business-default');
            }
        }

        return config('roles.default_store_slugs.customer', 'customers-default');
    }

    public function getDefaultStoreName(string $slug): string
    {
        return match ($slug) {
            'business-default' => 'Business Default',
            'customers-default' => 'Customers Default',
            default => 'Default Store',
        };
    }

    public function getDefaultStoreSettings(string $slug, array $userRoles): array
    {
        $baseSettings = config('settings.base_settings', []);
        $storeTypeSettings = config("settings.store_type_settings.{$slug}", []);

        return array_merge($baseSettings, $storeTypeSettings);
    }
}
