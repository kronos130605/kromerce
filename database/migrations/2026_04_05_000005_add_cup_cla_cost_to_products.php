<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_cup_amount', 12, 2)->nullable()->after('historical_cost_date');
            $table->decimal('cost_cup_rate', 12, 6)->nullable()->after('cost_cup_amount');
            $table->date('cost_cup_date')->nullable()->after('cost_cup_rate');
            $table->decimal('cost_cla_amount', 12, 2)->nullable()->after('cost_cup_date');
            $table->decimal('cost_cla_rate', 12, 6)->nullable()->after('cost_cla_amount');
            $table->date('cost_cla_date')->nullable()->after('cost_cla_rate');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'cost_cup_amount', 'cost_cup_rate', 'cost_cup_date',
                'cost_cla_amount', 'cost_cla_rate', 'cost_cla_date',
            ]);
        });
    }
};
