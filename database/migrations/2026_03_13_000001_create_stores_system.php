<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stores table - Main store information
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            
            // Business information
            $table->string('business_type', 20)->default('retail'); // retail, wholesale, marketplace
            $table->string('status', 20)->default('active'); // active, inactive, maintenance, suspended
            $table->string('tax_id')->nullable(); // NIT/RUC for invoicing
            $table->boolean('verified_business')->default(false);
            
            // Online presence
            $table->string('website_url')->nullable();
            $table->string('timezone')->default('America/Havana');
            
            // Metadata
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['status', 'business_type']);
            $table->index('owner_id');
            $table->index('slug');
        });

        // Store Contacts - Normalized contact information
        Schema::create('store_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('type'); // email, phone, address, whatsapp
            $table->string('value'); // correo@ejemplo.com, +5355555555, dirección completa
            $table->string('label')->nullable(); // 'Soporte', 'Ventas', 'Principal', 'WhatsApp'
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_public')->default(true); // Visible to customers
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'type', 'is_primary']);
            $table->index(['store_id', 'is_public']);
        });

        // Store Social Media - Social media profiles
        Schema::create('store_social_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('platform'); // facebook, instagram, twitter, linkedin, tiktok, youtube
            $table->string('url');
            $table->string('handle')->nullable(); // @tienda_ejemplo
            $table->integer('followers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'platform']);
            $table->index(['store_id', 'is_active', 'sort_order']);
        });

        // Store Payment Methods - Available payment methods per store
        Schema::create('store_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('method'); // stripe, paypal, zelle, crypto, bank_transfer, cash_on_delivery
            $table->string('provider')->nullable(); // 'stripe', 'paypal', 'manual', 'tropipay'
            $table->json('config'); // Only for method-specific configuration
            $table->boolean('is_enabled')->default(true);
            $table->decimal('min_amount', 10, 2)->nullable(); // Minimum amount for this method
            $table->decimal('max_amount', 10, 2)->nullable(); // Maximum amount for this method
            $table->decimal('fee_percentage', 5, 2)->default(0); // Fee percentage
            $table->decimal('fixed_fee', 8, 2)->default(0); // Fixed fee
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'is_enabled', 'sort_order']);
        });

        // Store Shipping Zones - Delivery zones and costs
        Schema::create('store_shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name'); // 'La Habana', 'Provincia Occidente', 'Nacional'
            $table->json('locations'); // Array of provinces/municipalities
            $table->decimal('cost', 8, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Free shipping over this amount
            $table->integer('delivery_days_min')->nullable();
            $table->integer('delivery_days_max')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'is_active', 'sort_order']);
        });

        // Store Pickup Locations - Physical pickup points
        Schema::create('store_pickup_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name'); // 'Tienda Principal', 'Punto de Venta Centro Habana'
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('instructions')->nullable(); // Pickup instructions
            $table->json('schedule')->nullable(); // JSON with opening hours
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'is_active']);
        });

        // Store Business Hours - Operating hours
        Schema::create('store_business_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('day'); // monday, tuesday, wednesday, thursday, friday, saturday, sunday
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_closed')->default(false);
            $table->json('breaks')->nullable(); // Break periods during the day
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'day']);
        });

        // Store Settings - Store-specific configurations (minimal JSON)
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('key'); // 'theme_primary_color', 'email_notifications_enabled', 'auto_confirm_orders'
            $table->json('value'); // Only for simple configuration values
            $table->string('type')->default('string'); // string, boolean, number, json
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Whether this setting can be accessed publicly
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'key']);
        });

        // Store Currency Config - Currency configuration per store
        Schema::create('store_currency_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('default_currency', 3); // USD, EUR, CUP
            $table->json('display_currencies'); // ["USD", "EUR", "CUP"]
            $table->boolean('use_custom_rates')->default(false);
            $table->boolean('auto_update_rates')->default(false); // false for CUP
            $table->string('rate_update_frequency', 20)->default('weekly'); // daily, weekly, monthly
            $table->date('last_rate_update')->nullable();
            $table->integer('historical_retention_years')->default(2);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique('store_id');
        });

        // Store Users - Relationship between stores and users (extended from tenant_users)
        Schema::create('store_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('customer'); // owner, admin, manager, employee, customer
            $table->json('permissions')->nullable(); // Additional permissions beyond role
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'user_id']);
            $table->index(['store_id', 'role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_users');
        Schema::dropIfExists('store_currency_configs');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('store_business_hours');
        Schema::dropIfExists('store_pickup_locations');
        Schema::dropIfExists('store_shipping_zones');
        Schema::dropIfExists('store_payment_methods');
        Schema::dropIfExists('store_social_media');
        Schema::dropIfExists('store_contacts');
        Schema::dropIfExists('stores');
    }
};
