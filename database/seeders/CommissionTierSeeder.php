<?php

namespace Database\Seeders;

use App\Models\CommissionTier;
use Illuminate\Database\Seeder;

class CommissionTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Starter',
                'description' => 'Default commission tier for new stores',
                'category_id' => null,
                'percentage' => 15.00,
                'fixed_amount' => 0.00,
                'min_order_value' => null,
                'max_order_value' => null,
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'description' => 'Standard commission tier for established stores',
                'category_id' => null,
                'percentage' => 10.00,
                'fixed_amount' => 0.00,
                'min_order_value' => null,
                'max_order_value' => null,
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Reduced commission for high-volume stores',
                'category_id' => null,
                'percentage' => 7.50,
                'fixed_amount' => 0.00,
                'min_order_value' => null,
                'max_order_value' => null,
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Lowest commission for enterprise partners',
                'category_id' => null,
                'percentage' => 5.00,
                'fixed_amount' => 0.00,
                'min_order_value' => null,
                'max_order_value' => null,
                'priority' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            CommissionTier::create($tier);
        }
    }
}
