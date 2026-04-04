<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sale_currencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->string('currency_code', 3);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'currency_code']);
            $table->index(['product_id', 'is_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sale_currencies');
    }
};
