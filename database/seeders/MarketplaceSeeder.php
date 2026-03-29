<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Seed the marketplace system tables.
     */
    public function run(): void
    {
        $this->call([
            CommissionTierSeeder::class,
            StoreBadgeSeeder::class,
            ModerationRuleSeeder::class,
        ]);
    }
}
