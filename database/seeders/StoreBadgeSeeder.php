<?php

namespace Database\Seeders;

use App\Models\StoreBadge;
use Illuminate\Database\Seeder;

class StoreBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Top Seller',
                'slug' => 'top-seller',
                'description' => 'Awarded to stores with exceptional sales performance',
                'icon' => 'trophy',
                'color' => '#FFD700',
                'type' => 'performance',
                'requirements' => [
                    'min_monthly_sales' => 10000,
                    'min_orders' => 100,
                    'min_rating' => 4.5,
                ],
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Verified Seller',
                'slug' => 'verified-seller',
                'description' => 'Store has been verified and authenticated',
                'icon' => 'shield-check',
                'color' => '#4CAF50',
                'type' => 'verification',
                'requirements' => [
                    'business_verified' => true,
                    'min_orders' => 10,
                ],
                'priority' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Fast Shipper',
                'slug' => 'fast-shipper',
                'description' => 'Consistently ships orders within 24 hours',
                'icon' => 'truck-fast',
                'color' => '#2196F3',
                'type' => 'shipping',
                'requirements' => [
                    'on_time_delivery_rate' => 95,
                    'min_orders' => 50,
                ],
                'priority' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Customer Favorite',
                'slug' => 'customer-favorite',
                'description' => 'Highly rated by customers',
                'icon' => 'heart',
                'color' => '#E91E63',
                'type' => 'rating',
                'requirements' => [
                    'min_rating' => 4.8,
                    'min_total_ratings' => 50,
                ],
                'priority' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Responsive Support',
                'slug' => 'responsive-support',
                'description' => 'Responds to customer inquiries quickly',
                'icon' => 'message-circle',
                'color' => '#9C27B0',
                'type' => 'support',
                'requirements' => [
                    'response_rate' => 90,
                    'average_response_time' => 24,
                ],
                'priority' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'New Store',
                'slug' => 'new-store',
                'description' => 'Recently joined the marketplace',
                'icon' => 'sparkles',
                'color' => '#FF9800',
                'type' => 'milestone',
                'requirements' => [
                    'days_active' => 30,
                ],
                'priority' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            StoreBadge::create($badge);
        }
    }
}
