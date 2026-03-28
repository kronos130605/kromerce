<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates additional system tables: login attempts, currencies, cache, jobs, etc.
     */
    public function up(): void
    {
        // Login attempts tracking for security
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->boolean('was_successful')->default(false);
            $table->timestamp('attempted_at');
            $table->string('failure_reason')->nullable(); // invalid_credentials, account_locked, invalid_2fa
            $table->string('device_fingerprint')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            
            $table->index(['email', 'attempted_at'], 'login_attempts_email_date');
            $table->index(['ip_address', 'attempted_at'], 'login_attempts_ip_date');
            $table->index('device_fingerprint');
        });

        // Currencies table (global reference)
        Schema::create('currencies', function (Blueprint $table) {
            $table->string('code', 3)->primary(); // USD, EUR, CUP
            $table->string('name'); // US Dollar, Euro, Cuban Peso
            $table->string('symbol'); // $, €, ₱
            $table->string('flag_emoji', 10)->nullable(); // 🇺🇸, 🇪🇺, 🇨🇺
            $table->boolean('is_active')->default(true);
            $table->boolean('is_crypto')->default(false);
            $table->integer('decimal_places')->default(2);
            $table->string('subunit_name')->nullable(); // cent, centavo
            $table->timestamps();
        });

        // Failed jobs (Laravel default)
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Job batches (Laravel default)
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->text('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // Jobs (Laravel default)
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Cache (Laravel default)
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Cache locks (Laravel default)
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // Sessions (Laravel default)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Personal access tokens (Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Activity log for store actions
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // product_created, order_updated, user_login
            $table->string('entity_type'); // Product, Order, User
            $table->string('entity_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['store_id', 'action', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // Notifications (for in-app notifications)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // API keys for store integrations
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('key', 64)->unique();
            $table->string('secret', 128)->unique();
            $table->json('permissions')->nullable(); // ['read:products', 'write:orders']
            $table->string('ip_whitelist')->nullable(); // Comma-separated IPs
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['store_id', 'is_active']);
            $table->index('key');
        });

        // Webhooks for store integrations
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->string('url');
            $table->string('method', 10)->default('POST');
            $table->json('events'); // ['order.created', 'product.updated']
            $table->string('secret')->nullable();
            $table->json('headers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('verify_ssl')->default(true);
            $table->integer('timeout')->default(30);
            $table->integer('retries')->default(3);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->timestamps();
            
            $table->index(['store_id', 'is_active']);
        });

        // Webhook deliveries log
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id');
            $table->string('event');
            $table->string('entity_type');
            $table->string('entity_id');
            $table->json('payload');
            $table->text('response_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->string('status', 20); // pending, delivered, failed, retrying
            $table->integer('attempt_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
            $table->index(['webhook_id', 'status', 'created_at']);
        });

        // System settings (global configuration)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->string('type', 20)->default('string'); // string, number, boolean, json
            $table->text('description')->nullable();
            $table->string('group', 50)->default('general'); // general, email, payment, security
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('login_attempts');
    }
};
