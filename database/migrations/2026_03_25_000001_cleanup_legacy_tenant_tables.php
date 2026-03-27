<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Elimina tablas legacy de tenants que ya fueron migradas a stores.
     * Las tablas tenants y tenant_users ya no se usan tras Fase 2.
     */
    public function up(): void
    {
        // Drop legacy tables (data ya migrada a stores/store_users)
        Schema::dropIfExists('tenant_users');
        Schema::dropIfExists('tenants');
    }

    /**
     * Reverse the migrations.
     *
     * Nota: Esto es para rollback de desarrollo, no recupera datos.
     */
    public function down(): void
    {
        // Recreate tables for rollback (empty)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('custom_domain')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('branding_config')->nullable();
            $table->json('data')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('customer');
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id']);
        });
    }
};
