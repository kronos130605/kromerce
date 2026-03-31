<?php

namespace Database\Seeders;

use App\Models\ModerationRule;
use Illuminate\Database\Seeder;

class ModerationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Prohibited Keywords',
                'type' => 'keyword_filter',
                'conditions' => [
                    'keywords' => ['scam', 'fake', 'counterfeit', 'replica'],
                    'fields' => ['name', 'description'],
                ],
                'action' => 'flag_for_review',
                'severity' => 3,
                'is_active' => true,
                'trigger_count' => 0,
            ],
            [
                'name' => 'Suspicious Pricing',
                'type' => 'price_check',
                'conditions' => [
                    'max_discount_percentage' => 90,
                    'min_price' => 0.01,
                ],
                'action' => 'flag_for_review',
                'severity' => 2,
                'is_active' => true,
                'trigger_count' => 0,
            ],
            [
                'name' => 'Missing Product Images',
                'type' => 'image_check',
                'conditions' => [
                    'min_images' => 1,
                ],
                'action' => 'auto_reject',
                'severity' => 1,
                'is_active' => true,
                'trigger_count' => 0,
            ],
            [
                'name' => 'Incomplete Product Info',
                'type' => 'completeness_check',
                'conditions' => [
                    'required_fields' => ['name', 'description', 'base_price', 'sku'],
                    'min_description_length' => 50,
                ],
                'action' => 'auto_reject',
                'severity' => 1,
                'is_active' => true,
                'trigger_count' => 0,
            ],
            [
                'name' => 'Duplicate Product Detection',
                'type' => 'duplicate_check',
                'conditions' => [
                    'check_fields' => ['name', 'sku'],
                    'similarity_threshold' => 95,
                ],
                'action' => 'flag_for_review',
                'severity' => 2,
                'is_active' => true,
                'trigger_count' => 0,
            ],
        ];

        foreach ($rules as $rule) {
            ModerationRule::firstOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}
