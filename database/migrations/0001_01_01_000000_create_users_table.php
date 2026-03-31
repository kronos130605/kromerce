<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the users table with all fields integrated (no separate add_field migrations needed).
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Store association (replaces tenant_id)
            $table->unsignedBigInteger('store_id')->nullable();
            
            // Basic information
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Profile information (from add_fields_to_users_table)
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 20)->nullable(); // male, female, other, prefer_not_to_say
            
            // Address information
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // User preferences and settings
            $table->string('locale', 5)->default('es'); // Language code: es, en
            $table->string('timezone')->default('America/Havana');
            $table->string('currency', 3)->default('USD');
            
            // Dark mode preference (from add_dark_mode_preferences_to_users_table)
            $table->string('theme', 20)->default('system'); // light, dark, system
            $table->boolean('dark_mode')->default(false);
            
            // Two-factor authentication (from add_two_factor_columns_to_users_table)
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            
            // Security and status
            $table->string('status', 20)->default('active'); // active, inactive, suspended, pending
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            // Marketing and notifications
            $table->boolean('newsletter_subscribed')->default(false);
            $table->timestamp('newsletter_subscribed_at')->nullable();
            $table->boolean('marketing_emails')->default(true);
            $table->boolean('sms_notifications')->default(false);
            
            // Metadata
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes (foreign key for store_id will be added later to avoid circular dependency)
            $table->index(['store_id', 'status']);
            $table->index(['email', 'status']);
            $table->index('username');
            $table->index(['country', 'city']);
        });

        // User sessions for tracking active sessions
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->nullable(); // desktop, mobile, tablet
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_country', 2)->nullable();
            $table->timestamp('last_activity_at');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['user_id', 'last_activity_at']);
            $table->index('session_id');
            $table->index('expires_at');
        });

        // User devices for trusted device management
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_fingerprint')->unique();
            $table->string('device_name');
            $table->string('device_type', 20); // desktop, mobile, tablet
            $table->string('os');
            $table->string('browser');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('trusted_at')->nullable();
            $table->timestamp('last_seen_at');
            $table->timestamps();
            
            $table->index(['user_id', 'is_trusted']);
            $table->index('device_fingerprint');
        });

        // Password reset tokens (extended from default)
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
        });

        // Email change history
        Schema::create('user_email_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('old_email');
            $table->string('new_email');
            $table->timestamp('changed_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_email_changes');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('users');
    }
};
