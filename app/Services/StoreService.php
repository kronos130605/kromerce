<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreContact;
use App\Models\StoreCurrencyConfig;
use App\Models\StorePaymentMethod;
use App\Models\User;
use App\Repositories\Store\StoreContactRepository;
use App\Repositories\Store\StoreCurrencyConfigRepository;
use App\Repositories\Store\StorePaymentMethodRepository;
use App\Repositories\Store\StoreRepository;
use App\Repositories\Store\StoreStatisticsRepository;
use App\Repositories\User\UserStoreRepository;
use App\Traits\HasStorePermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreService
{
    use HasStorePermissions;

    protected $storeRepository;
    private $storeContactRepository;
    private $storePaymentMethodRepository;
    private $storeCurrencyConfigRepository;
    private $storeStatisticsRepository;

    private $userStoreRepository;

    public function __construct(
        StoreRepository $storeRepository,
        StoreContactRepository $storeContactRepository,
        StorePaymentMethodRepository $storePaymentMethodRepository,
        StoreCurrencyConfigRepository $storeCurrencyConfigRepository,
        StoreStatisticsRepository $storeStatisticsRepository,
        UserStoreRepository $userStoreRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeContactRepository = $storeContactRepository;
        $this->storePaymentMethodRepository = $storePaymentMethodRepository;
        $this->storeCurrencyConfigRepository = $storeCurrencyConfigRepository;
        $this->storeStatisticsRepository = $storeStatisticsRepository;
        $this->userStoreRepository = $userStoreRepository;
    }
    /**
     * Create a new store with default configuration.
     */
    public function createStore(array $data): Store
    {
        try {
            DB::beginTransaction();

            $store = $this->storeRepository->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'logo' => $data['logo'] ?? null,
                'banner' => $data['banner'] ?? null,
                'business_type' => $data['business_type'] ?? 'retail',
                'status' => 'active',
                'tax_id' => $data['tax_id'] ?? null,
                'website_url' => $data['website_url'] ?? null,
                'timezone' => $data['timezone'] ?? 'America/Havana',
                'owner_id' => Auth::id(),
            ]);

            // Create default currency configuration
            $this->storeCurrencyConfigRepository->create([
                'store_id' => $store->id,
                'default_currency' => $data['default_currency'] ?? 'USD',
                'display_currencies' => $data['display_currencies'] ?? ['USD', 'EUR', 'CUP'],
                'use_custom_rates' => false,
                'auto_update_rates' => false,
                'rate_update_frequency' => 'weekly',
                'historical_retention_years' => 2,
            ]);

            DB::commit();

            return $store;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create store', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update store with currency configuration.
     */
    public function updateStoreCurrencyConfig(Store $store, array $currencyData): StoreCurrencyConfig
    {
        try {
            $config = $this->storeCurrencyConfigRepository->getFirstBy([
                'store_id' => $store->id
            ]);

            if ($config) {
                $this->storeCurrencyConfigRepository->update($config->id, [
                    'default_currency' => $currencyData['default_currency'],
                    'display_currencies' => $currencyData['display_currencies'],
                    'use_custom_rates' => $currencyData['use_custom_rates'] ?? false,
                    'auto_update_rates' => $currencyData['auto_update_rates'] ?? false,
                ]);
            } else {
                $config = $this->storeCurrencyConfigRepository->create([
                    'store_id' => $store->id,
                    'default_currency' => $currencyData['default_currency'],
                    'display_currencies' => $currencyData['display_currencies'],
                    'use_custom_rates' => $currencyData['use_custom_rates'] ?? false,
                    'auto_update_rates' => $currencyData['auto_update_rates'] ?? false,
                ]);
            }

            return $config;

        } catch (\Exception $e) {
            Log::error('Failed to update store currency config', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'currency_data' => $currencyData,
            ]);
            throw $e;
        }
    }

    /**
     * Add contact to store.
     */
    public function addContact(Store $store, array $contactData): StoreContact
    {
        try {
            return $this->storeContactRepository->create([
                'store_id' => $store->id,
                'type' => $contactData['type'],
                'value' => $contactData['value'],
                'label' => $contactData['label'] ?? null,
                'is_primary' => $contactData['is_primary'] ?? false,
                'is_public' => $contactData['is_public'] ?? true,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add contact to store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'contact_data' => $contactData,
            ]);
            throw $e;
        }
    }

    /**
     * Add payment method to store.
     */
    public function addPaymentMethod(Store $store, array $paymentData): StorePaymentMethod
    {
        try {
            return $this->storePaymentMethodRepository->create([
                'store_id' => $store->id,
                'method' => $paymentData['method'],
                'provider' => $paymentData['provider'] ?? 'manual',
                'config' => $paymentData['config'] ?? [],
                'is_enabled' => $paymentData['is_enabled'] ?? true,
                'min_amount' => $paymentData['min_amount'] ?? null,
                'max_amount' => $paymentData['max_amount'] ?? null,
                'fee_percentage' => $paymentData['fee_percentage'] ?? 0,
                'fixed_fee' => $paymentData['fixed_fee'] ?? 0,
                'sort_order' => $paymentData['sort_order'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add payment method to store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);
            throw $e;
        }
    }

    /**
     * Get store statistics.
     */
    public function getStoreStatistics(Store $store): array
    {
        return [
            'products_count' => $this->storeStatisticsRepository->getProductsCount($store->id),
            'active_products_count' => $this->storeStatisticsRepository->getActiveProductsCount($store->id),
            'categories_count' => $this->storeStatisticsRepository->getCategoriesCount($store->id),
            'orders_count' => $this->storeStatisticsRepository->getOrdersCount($store->id),
            'total_revenue' => $this->storeStatisticsRepository->getTotalRevenue($store->id),
            'recent_orders' => $this->storeStatisticsRepository->getRecentOrders($store->id),
            'low_stock_products' => $this->storeStatisticsRepository->getLowStockProductsCount($store->id),
        ];
    }

    /**
     * Toggle store status.
     */
    public function toggleStoreStatus(Store $store): string
    {
        try {
            $newStatus = $store->status === 'active' ? 'inactive' : 'active';

            $store->update(['status' => $newStatus]);

            Log::info('Store status toggled', [
                'store_id' => $store->id,
                'old_status' => $store->status,
                'new_status' => $newStatus,
            ]);

            return $newStatus;

        } catch (\Exception $e) {
            Log::error('Failed to toggle store status', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify store.
     */
    public function verifyStore(Store $store): Store
    {
        try {
            $store->update(['verified_business' => true]);

            Log::info('Store verified', [
                'store_id' => $store->id,
                'name' => $store->name,
            ]);

            return $store;

        } catch (\Exception $e) {
            Log::error('Failed to verify store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if user can manage store.
     */
    public function canUserManageStore(int $userId, Store $store): bool
    {
        $user = Auth::user();

        // Store owners can always manage their stores
        if ($store->owner_id === $userId) {
            return true;
        }

        // Admins can manage all stores
        if ($user && $user->hasRole('admin')) {
            return true;
        }

        // Store managers can update but not delete
        if ($user && $user->hasRole('manager')) {
            return true;
        }

        return false;
    }

    /**
     * Get stores accessible to user.
     */
    public function getAccessibleStores(): Collection
    {
        $user = Auth::user();

        return Store::query()
            ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->where('is_active', true);
                    });
            })
            ->with(['owner', 'currencyConfig'])
            ->get();
    }

    /**
     * Get or create default store for user based on role
     */
    public function getOrCreateDefaultStoreForUser(User $user): ?Store
    {
        try {
            $userRoles = $user->roles->pluck('name')->toArray();

            // Determinar store slug según rol prioritario
            $storeSlug = $this->getDefaultStoreSlugForRoles($userRoles);

            // Buscar store existente
            $store = $this->userStoreRepository->getUserFirstStore($user);

            if (!$store) {
                // If no store found for user, check if default store exists by slug
                $defaultStoreSlug = $this->getDefaultStoreSlugForRoles($userRoles);
                $store = $this->storeRepository->getFirstBy(['slug' => $defaultStoreSlug]);

                if (!$store) {
                    // Create store por defecto
                    $store = $this->createDefaultStore($defaultStoreSlug, $userRoles, $user);
                } else {
                    // Associate existing default store with user
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

    /**
     * Assign store to user with appropriate role
     */
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

    /**
     * Get default store slug based on user roles
     */
    public function getDefaultStoreSlugForRoles(array $userRoles): string
    {
        // Get business roles from config
        $businessRoles = config('roles.business_roles', ['business_owner']);

        // Check if user has any business role
        foreach ($businessRoles as $role) {
            if (in_array($role, $userRoles)) {
                return config('roles.default_store_slugs.business', 'business-default');
            }
        }

        // Default to customer store
        return config('roles.default_store_slugs.customer', 'customers-default');
    }

    /**
     * Create default store with appropriate settings
     */
    public function createDefaultStore(string $slug, array $userRoles, ?User $user = null): ?Store
    {
        try {
            $settings = $this->getDefaultStoreSettings($slug, $userRoles);
            $uuid = \Illuminate\Support\Str::uuid();

            // Usar el usuario actual como owner, o buscar uno disponible
            $ownerId = $user?->id ?? $this->storeRepository->getAvailableOwnerId();

            if (!$ownerId) {
                throw new \Exception('No available user found for owner_id');
            }

            // Usar repositorio para crear store
            $storeId = $this->storeRepository->createDirect([
                'uuid' => $uuid,
                'name' => $this->getDefaultStoreName($slug),
                'slug' => $slug,
                'data' => json_encode($settings),
                'status' => 'active',
                'owner_id' => $ownerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Obtener el store creado
            $store = $this->storeRepository->getFirstBy([
                'id' => $storeId
            ]);

            if (!$store) {
                throw new \Exception('Failed to retrieve created store');
            }

            return $store;

        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), $slug)) {
                Log::warning('Store with slug already exists, attempting to retrieve existing store', [
                    'slug' => $slug,
                    'user_roles' => $userRoles,
                    'error' => $e->getMessage(),
                ]);

                // Try to get existing store by slug
                $existingStore = $this->storeRepository->getFirstBy(['slug' => $slug]);
                if ($existingStore && $user) {
                    // Associate user with existing store
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

    /**
     * Get default store name
     */
    public function getDefaultStoreName(string $slug): string
    {
        return match ($slug) {
            'business-default' => 'Business Default',
            'customers-default' => 'Customers Default',
            default => 'Default Store',
        };
    }

    /**
     * Get default store settings
     */
    public function getDefaultStoreSettings(string $slug, array $userRoles): array
    {
        // Get base settings from config
        $baseSettings = config('settings.base_settings');

        // Get store type specific settings from config
        $storeTypeSettings = config("settings.store_type_settings.{$slug}", []);

        // Merge base settings with store type specific settings
        return array_merge($baseSettings, $storeTypeSettings);
    }

    /**
     * Get available themes from config
     */
    public function getAvailableThemes(): array
    {
        return config('settings.themes');
    }

    /**
     * Get theme configuration by name
     */
    public function getThemeConfig(string $themeName): ?array
    {
        return config("settings.themes.{$themeName}");
    }

    /**
     * Get supported currencies from config
     */
    public function getSupportedCurrencies(): array
    {
        return config('settings.currencies');
    }

    /**
     * Get currency configuration by code
     */
    public function getCurrencyConfig(string $currencyCode): ?array
    {
        return config("settings.currencies.{$currencyCode}");
    }

    /**
     * Get supported languages from config
     */
    public function getSupportedLanguages(): array
    {
        return config('settings.languages');
    }

    /**
     * Get language configuration by code
     */
    public function getLanguageConfig(string $languageCode): ?array
    {
        return config("settings.languages.{$languageCode}");
    }

    /**
     * Get common timezones from config
     */
    public function getCommonTimezones(): array
    {
        return config('settings.timezones');
    }

    /**
     * Get layout defaults for store type
     */
    public function getLayoutDefaults(string $storeType): array
    {
        return config("settings.layout_defaults.{$storeType}", []);
    }

    public function resolveCurrentStoreForRequest(Request $request): ?Store
    {
        $hostname = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        if (!in_array($hostname, $centralDomains)) {
            $store = Store::whereHas('domains', function ($query) use ($hostname) {
                $query->where('domain', $hostname);
            })->first();

            if ($store) {
                session(['current_store_id' => $store->id]);
                return $store;
            }
        }

        $user = $request->user();
        if (!$user) {
            return null;
        }

        $sessionStoreId = session('current_store_id');
        if ($sessionStoreId) {
            $hasAccess = $user->stores()->where('stores.id', $sessionStoreId)->exists();
            if ($hasAccess) {
                $store = Store::find($sessionStoreId);
                if ($store) {
                    return $store;
                }
            }
        }

        $store = $this->getUserCurrentStore($user) ?: $this->userStoreRepository->getUserFirstStore($user);
        if ($store) {
            session(['current_store_id' => $store->id]);
        }

        return $store;
    }

    /**
     * Get feature flags for store type
     */
    public function getFeatureFlags(string $storeType): array
    {
        return config("settings.features.{$storeType}", []);
    }

    /**
     * Get user's current store
     */
    public function getUserCurrentStore(User $user): ?Store
    {
        return $this->userStoreRepository->getUserCurrentStore($user);
    }

    /**
     * Set user's current store
     */
    public function setUserCurrentStore(User $user, Store $store): bool
    {
        return $this->userStoreRepository->setUserCurrentStore($user, $store);
    }

    /**
     * Get all stores for user
     */
    public function getUserStores(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userStoreRepository->getUserStores($user);
    }

    /**
     * Get basic store data for frontend (optimized for Inertia).
     * Uses caching to avoid repeated database queries.
     */
    public function getBasicStoreDataForFrontend(int $storeId): ?array
    {
        // Cache for 30 minutes to improve performance
        $cacheKey = "store_basic_data_{$storeId}";

        return cache()->remember($cacheKey, 1800, function () use ($storeId) {
            return $this->storeRepository->getBasicStoreData($storeId);
        });
    }
}
