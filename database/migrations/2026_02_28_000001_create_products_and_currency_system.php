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
        // Configuración de monedas por business
        Schema::create('business_currency_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('default_currency', 3);
            $table->json('display_currencies');
            $table->boolean('use_custom_rates')->default(false);
            $table->boolean('auto_update_rates')->default(true);
            $table->string('rate_update_frequency', 20)->default('daily');
            $table->date('last_rate_update')->nullable();
            $table->integer('historical_retention_years')->default(2);
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique('tenant_id');
        });

        // Tasas globales (default)
        Schema::create('currency_rates_global', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 10, 6);
            $table->date('effective_date');
            $table->string('source', 50)->default('api');
            $table->timestamps();
            
            $table->unique(['from_currency', 'to_currency', 'effective_date'], 'cr_global_unique');  // ← ACORTADO
            $table->index(['from_currency', 'to_currency', 'effective_date'], 'cr_global_index');  // ← ACORTADO
            $table->index('effective_date', 'cr_global_date');  // ← ACORTADO
        });

        // Tasas personalizadas por business
        Schema::create('currency_rates_business', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 10, 6);
            $table->date('effective_date');
            $table->string('source', 50)->default('manual');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'from_currency', 'to_currency', 'effective_date'], 'cr_business_unique');  // ← ACORTADO
        });

        // Categorías de productos
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->integer('level')->default(0);
            $table->integer('order')->default(0);
            $table->string('status', 20)->default('active');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->unique(['tenant_id', 'slug'], 'pc_tenant_slug_unique');  // ← ACORTADO
            $table->index(['tenant_id', 'status', 'parent_id'], 'pc_tenant_status_parent');  // ← ACORTADO
        });

        // Productos
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Precios base
            $table->string('base_currency', 3);
            $table->decimal('base_price', 10, 2);
            $table->decimal('base_sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            
            // Configuración de ofertas
            $table->boolean('is_on_sale')->default(false);
            $table->string('sale_type', 20)->nullable(); // 'fixed' o 'percentage'
            $table->decimal('sale_discount', 5, 2)->nullable();
            $table->date('sale_start_date')->nullable();
            $table->date('sale_end_date')->nullable();
            
            // Histórico de costos
            $table->string('historical_cost_currency', 3)->nullable();
            $table->decimal('historical_cost_amount', 10, 2)->nullable();
            $table->decimal('historical_cost_rate', 10, 6)->nullable();
            $table->date('historical_cost_date')->nullable();
            
            // Configuración
            $table->boolean('track_cost')->default(false);
            $table->boolean('show_cost_to_customer')->default(false);
            
            // SKU y barras
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            
            // Estado y visibilidad
            $table->string('status', 20)->default('active'); // active, inactive, draft
            $table->string('visibility', 20)->default('public'); // public, private, hidden
            $table->boolean('featured')->default(false);
            $table->boolean('downloadable')->default(false);
            $table->boolean('virtual')->default(false);
            
            // Tipo de producto
            $table->string('product_type', 20)->default('simple'); // simple, variable, grouped
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status', 20)->default('instock'); // instock, outofstock, onbackorder
            $table->integer('low_stock_threshold')->default(0);
            
            // Shipping
            $table->decimal('weight', 8, 2)->nullable(); // kg
            $table->decimal('length', 8, 2)->nullable(); // cm
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('shipping_class')->nullable();
            $table->boolean('free_shipping')->default(false);
            
            // Taxes
            $table->string('tax_class')->nullable();
            $table->string('tax_status', 20)->default('taxable'); // taxable, exempt
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();  // ← CAMBIADO: BIGINT para referenciar users.id
            $table->unsignedBigInteger('updated_by')->nullable();  // ← CAMBIADO: BIGINT para referenciar users.id
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['tenant_id', 'slug'], 'p_tenant_slug_unique');  // ← ACORTADO
            $table->unique(['tenant_id', 'sku'], 'p_tenant_sku_unique');  // ← ACORTADO
            $table->index(['tenant_id', 'status'], 'p_tenant_status');  // ← ACORTADO
            $table->index(['tenant_id', 'stock_status'], 'p_tenant_stock');  // ← ACORTADO
            $table->index(['tenant_id', 'featured'], 'p_tenant_featured');  // ← ACORTADO
        });

        // Relación productos-categorías
        Schema::create('product_category_product', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('category_id');
            $table->integer('order')->default(0);
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->primary(['product_id', 'category_id']);
        });

        // Tags de productos
        Schema::create('product_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable();
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'slug'], 'pt_tenant_slug_unique');  // ← ACORTADO
        });

        // Relación productos-tags
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('tag_id');
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('product_tags')->onDelete('cascade');
            $table->primary(['product_id', 'tag_id']);
        });

        // Imágenes de productos
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->uuid('product_id');
            $table->string('url');
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'order'], 'pi_product_order');  // ← ACORTADO
        });

        // Atributos de productos (para variantes)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('tenant_id');  // ← CAMBIADO: BIGINT para referenciar tenants.id
            $table->string('name');
            $table->string('slug');
            $table->string('type', 20)->default('text'); // text, color, number, visual
            $table->boolean('required')->default(false);
            $table->json('config')->nullable(); // Configuración adicional
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'slug'], 'pa_tenant_slug_unique');  // ← ACORTADO
        });

        // Valores de atributos
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->uuid('attribute_id');
            $table->string('value');
            $table->string('label');
            $table->string('color', 7)->nullable(); // Para atributos de color
            $table->string('image')->nullable(); // Para atributos visuales
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->unique(['attribute_id', 'value']);
        });

        // Variantes de productos
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->uuid('product_id');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            
            // Precios (pueden diferir del producto principal)
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('base_sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            
            // Inventario
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status', 20)->default('instock');
            $table->integer('low_stock_threshold')->default(0);
            
            // Shipping
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            
            // Estado
            $table->string('status', 20)->default('active');
            $table->boolean('enabled')->default(true);
            
            // Metadata
            $table->json('attributes')->nullable(); // {color: 'red', size: 'M'}
            $table->timestamps();
            $table->softDeletes();  // ← AGREGADO: SoftDeletes column
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'sku'], 'pv_product_sku_unique');  // ← ACORTADO
            $table->index(['product_id', 'status'], 'pv_product_status');  // ← ACORTADO
        });

        // Imágenes de variantes
        Schema::create('product_variant_images', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->unsignedBigInteger('variant_id');  // ← CAMBIADO: BIGINT para referenciar product_variants.id
            $table->string('url');
            $table->string('alt')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['variant_id', 'order'], 'pvi_variant_order');  // ← ACORTADO
        });

        // Historial de precios de productos
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->uuid('product_id');
            $table->string('currency', 3);
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->string('change_reason', 100); // 'manual', 'rate_update', 'sale_start', 'sale_end'
            $table->unsignedBigInteger('changed_by')->nullable();  // ← CAMBIADO: BIGINT para referenciar users.id
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['product_id', 'currency', 'created_at'], 'pph_product_currency_date');  // ← ACORTADO
        });

        // Auditoría de actualizaciones de tasas
        Schema::create('currency_rate_updates', function (Blueprint $table) {
            $table->id();  // ← CAMBIADO: Autoincremental para performance
            $table->date('update_date');
            $table->json('currencies_updated'); // ['USD-EUR', 'USD-GBP', ...]
            $table->string('source', 50); // 'api', 'manual', 'import'
            $table->boolean('success');
            $table->text('error_message')->nullable();
            $table->integer('total_rates_updated')->default(0);
            $table->timestamps();
            
            $table->unique('update_date', 'cru_update_date_unique');  // ← ACORTADO
            $table->index('success', 'cru_success');  // ← ACORTADO
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rate_updates');
        Schema::dropIfExists('product_price_history');
        Schema::dropIfExists('product_variant_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_product_tag');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('currency_rates_business');
        Schema::dropIfExists('currency_rates_global');
        Schema::dropIfExists('business_currency_configs');
    }
};
