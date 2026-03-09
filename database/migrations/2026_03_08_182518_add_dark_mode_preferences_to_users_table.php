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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('dark_mode')->default(false)->after('is_active');
            $table->json('theme_preferences')->nullable()->after('dark_mode');
            $table->string('language')->default('en')->after('theme_preferences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dark_mode', 'theme_preferences', 'language']);
        });
    }
};
