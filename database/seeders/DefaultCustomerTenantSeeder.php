<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefaultCustomerTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // Crear tenant por defecto para customers
            $customerTenant = Tenant::firstOrCreate([
                'slug' => 'customers-default',
            ], [
                'name' => 'Customers Default',
                'type' => 'customer',
                'settings' => [
                    'theme' => 'default',
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#10B981',
                    'accent_color' => '#F59E0B',
                    'show_flash_sales' => true,
                    'show_featured_stores' => true,
                    'show_ai_recommendations' => true,
                    'default_currency' => 'USD',
                    'language' => 'es',
                    'timezone' => 'America/Mexico_City',
                    'enable_notifications' => true,
                    'enable_wishlist' => true,
                    'enable_reviews' => true,
                    'special_events' => [],
                    'banners' => [
                        'welcome_banner' => true,
                        'flash_sale_banner' => true,
                        'seasonal_banner' => false,
                    ],
                    'layout' => [
                        'sidebar_position' => 'left',
                        'product_grid_columns' => 4,
                        'show_product_ratings' => true,
                        'show_product_compare' => true,
                    ],
                    'analytics' => [
                        'track_page_views' => true,
                        'track_user_behavior' => true,
                        'track_purchase_history' => true,
                    ],
                ],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar todos los usuarios customers a este tenant
            $customerUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'customer');
            })->get();

            foreach ($customerUsers as $user) {
                // Verificar si ya tiene un tenant
                if (!$user->tenants()->where('tenant_id', $customerTenant->id)->exists()) {
                    $user->tenants()->attach($customerTenant->id, [
                        'role' => 'customer',
                        'joined_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Establecer como tenant actual si no tiene uno
                    if (!$user->current_store_id) {
                        $user->current_store_id = $customerTenant->id;
                        $user->save();
                    }
                }
            }

            DB::commit();

            Log::info('Default customer tenant created successfully', [
                'tenant_id' => $customerTenant->id,
                'tenant_slug' => $customerTenant->slug,
                'users_assigned' => $customerUsers->count(),
            ]);

            $this->command->info('✅ Default customer tenant created successfully!');
            $this->command->info("📊 {$customerUsers->count()} customer users assigned to default tenant");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating default customer tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->command->error('❌ Error creating default customer tenant: ' . $e->getMessage());
        }
    }
}
