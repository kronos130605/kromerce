<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates Spatie Permission tables with store-scoped roles.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        // Teams/Stores table - using existing stores table instead of creating new teams table
        // We use store_id for team/scope context

        // Permissions table
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // Roles table with store scope
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id')->nullable(); // NULL for global roles, store_id for store-specific
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            // Store-scoped unique constraint
            $table->unique(['store_id', 'name', 'guard_name']);
            $table->index('store_id');
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Model permissions (polymorphic)
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');
        });

        // Model roles (polymorphic) with store scope
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->unsignedBigInteger('store_id')->nullable(); // Store context for the role assignment
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_index');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
                
            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type', 'store_id'],
                'model_has_roles_role_model_type_store_primary');
                
            $table->index(['store_id', 'role_id'], 'model_has_roles_store_role_index');
        });

        // Role permissions
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_role_primary');
        });

        // Permission categories for organization
        Schema::create('permission_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add category to permissions
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->text('description')->nullable()->after('name');
            $table->string('module', 50)->nullable()->after('description'); // users, products, orders, etc.
            $table->index(['category_id', 'module']);
            
            $table->foreign('category_id')->references('id')->on('permission_categories')->onDelete('set null');
        });

        // Role templates for quick role creation
        Schema::create('role_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions'); // Array of permission names
            $table->boolean('is_system')->default(false); // Can't be deleted if true
            $table->timestamps();
        });

        // Store role assignments tracking
        Schema::create('store_user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('role_id');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable(); // For temporary role assignments
            $table->json('custom_permissions')->nullable(); // Override/additional permissions
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['store_id', 'user_id', 'role_id'], 'store_user_roles_unique');
            $table->index(['store_id', 'user_id']);
        });

        // Permission audit log
        Schema::create('permission_audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // grant, revoke, role_assign, role_remove
            $table->string('subject_type'); // user, role
            $table->unsignedBigInteger('subject_id');
            $table->string('object_type'); // permission, role
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->json('previous_state')->nullable();
            $table->json('new_state')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('performed_at');
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['store_id', 'performed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            return;
        }

        Schema::dropIfExists('permission_audit_log');
        Schema::dropIfExists('store_user_roles');
        Schema::dropIfExists('role_templates');
        Schema::dropIfExists('permission_categories');
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
