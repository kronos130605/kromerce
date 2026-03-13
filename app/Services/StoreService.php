<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreContact;
use App\Models\StorePaymentMethod;
use App\Models\StoreCurrencyConfig;
use App\Models\StoreUser;
use App\Repositories\StoreRepository;
use App\Traits\HasStorePermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreService
{
    use HasStorePermissions;

    protected $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
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
            $store->currencyConfig()->create([
                'default_currency' => $data['default_currency'] ?? 'USD',
                'display_currencies' => $data['display_currencies'] ?? ['USD', 'EUR', 'CUP'],
                'use_custom_rates' => false,
                'auto_update_rates' => false,
                'rate_update_frequency' => 'weekly',
                'historical_retention_years' => 2,
            ]);

            DB::commit();

            Log::info('Store created', [
                'store_id' => $store->id,
                'name' => $store->name,
                'owner_id' => $store->owner_id,
            ]);

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
            $store->currencyConfig()->update([
                'default_currency' => $currencyData['default_currency'],
                'display_currencies' => $currencyData['display_currencies'],
                'use_custom_rates' => $currencyData['use_custom_rates'] ?? false,
                'auto_update_rates' => $currencyData['auto_update_rates'] ?? false,
            ]);

            Log::info('Store currency config updated', [
                'store_id' => $store->id,
                'currencies' => $currencyData['display_currencies'],
            ]);

            return $store->currencyConfig;

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
            $contact = $store->contacts()->create([
                'type' => $contactData['type'],
                'value' => $contactData['value'],
                'label' => $contactData['label'] ?? null,
                'is_primary' => $contactData['is_primary'] ?? false,
                'is_public' => $contactData['is_public'] ?? true,
            ]);

            Log::info('Contact added to store', [
                'store_id' => $store->id,
                'contact_type' => $contactData['type'],
                'contact_value' => $contactData['value'],
            ]);

            return $contact;

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
            $paymentMethod = $store->paymentMethods()->create([
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

            Log::info('Payment method added to store', [
                'store_id' => $store->id,
                'payment_method' => $paymentData['method'],
                'provider' => $paymentData['provider'],
            ]);

            return $paymentMethod;

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
            'products_count' => $store->products()->count(),
            'active_products' => $store->products()->where('status', 'active')->count(),
            'total_contacts' => $store->contacts()->count(),
            'primary_contacts' => $store->contacts()->where('is_primary', true)->count(),
            'public_contacts' => $store->contacts()->where('is_public', true)->count(),
            'total_payment_methods' => $store->paymentMethods()->count(),
            'active_payment_methods' => $store->paymentMethods()->where('is_enabled', true)->count(),
            'total_users' => $store->users()->count(),
            'active_users' => $store->users()->where('is_active', true)->count(),
            'created_at' => $store->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $store->updated_at->format('Y-m-d H:i:s'),
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
    public function getAccessibleStores()
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
}
