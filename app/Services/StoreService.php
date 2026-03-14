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
                // Crear store por defecto
                $store = $this->createDefaultStore($storeSlug, $userRoles);
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
    public function assignStoreToUser(User $user, Store $store, ?string $role = null): bool
    {
        try {
            $role = $role ?? $this->getDefaultRoleForUser($user);

            // Usar UserTenantRepository para asignar store
            return $this->userStoreRepository->attachUserToStore($user, $store, $role);

        } catch (\Exception $e) {
            Log::error('Error assigning store to user', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get default role for user in store
     */
    public function getDefaultRoleForUser(User $user): string
    {
        $userRoles = $user->roles->pluck('name')->toArray();

        // Prioridad de roles para store
        $rolePriority = [
            'business_owner' => 1,
            'admin' => 2,
            'manager' => 3,
            'employee' => 4,
            'customer' => 5,
        ];

        $defaultRole = 'customer';
        $highestPriority = PHP_INT_MAX;

        foreach ($userRoles as $role) {
            if (isset($rolePriority[$role]) && $rolePriority[$role] < $highestPriority) {
                $defaultRole = $role;
                $highestPriority = $rolePriority[$role];
            }
        }

        return $defaultRole;
    }

    /**
     * Get default store slug based on user roles
     */
    public function getDefaultStoreSlugForRoles(array $userRoles): string
    {
        // Si tiene rol de negocio, usar store de negocio por defecto
        $businessRoles = ['business_owner', 'admin', 'manager', 'employee'];

        foreach ($businessRoles as $role) {
            if (in_array($role, $userRoles)) {
                return 'business-default';
            }
        }

        // Si solo es customer, usar store de customer por defecto
        return 'customers-default';
    }

    /**
     * Create default store with appropriate settings
     */
    public function createDefaultStore(string $slug, array $userRoles): ?Store
    {
        try {
            $settings = $this->getDefaultStoreSettings($slug, $userRoles);
            $uuid = \Illuminate\Support\Str::uuid();

            // Obtener el primer usuario disponible como owner
            $ownerId = $this->storeRepository->getAvailableOwnerId();

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
                'store_id' => $storeId
            ]);

            if (!$store) {
                throw new \Exception('Failed to retrieve created store');
            }

            return $store;

        } catch (\Exception $e) {
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
        $baseSettings = [
            'theme' => 'default',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'default_currency' => 'USD',
            'language' => 'es',
            'timezone' => 'America/Mexico_City',
            'enable_notifications' => true,
            'created_by_system' => true,
        ];

        if ($slug === 'customers-default') {
            return array_merge($baseSettings, [
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
            ]);
        }

        if ($slug === 'business-default') {
            return array_merge($baseSettings, [
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
            ]);
        }

        return $baseSettings;
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
}
