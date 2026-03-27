<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'flag_emoji' => '🇺🇸',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'flag_emoji' => '🇪🇺',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'CUP',
                'name' => 'Cuban Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇺',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'flag_emoji' => '🇬🇧',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'flag_emoji' => '🇯🇵',
                'decimal_places' => 0,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
            [
                'code' => 'MXN',
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'flag_emoji' => '🇲🇽',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 6,
            ],
            [
                'code' => 'COP',
                'name' => 'Colombian Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇴',
                'decimal_places' => 0,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 7,
            ],
            [
                'code' => 'ARS',
                'name' => 'Argentine Peso',
                'symbol' => '$',
                'flag_emoji' => '🇦🇷',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 8,
            ],
            [
                'code' => 'BRL',
                'name' => 'Brazilian Real',
                'symbol' => 'R$',
                'flag_emoji' => '🇧🇷',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 9,
            ],
            [
                'code' => 'PEN',
                'name' => 'Peruvian Sol',
                'symbol' => 'S/',
                'flag_emoji' => '🇵🇪',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 10,
            ],
            [
                'code' => 'CLP',
                'name' => 'Chilean Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇱',
                'decimal_places' => 0,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 11,
            ],
            [
                'code' => 'UYU',
                'name' => 'Uruguayan Peso',
                'symbol' => '$U',
                'flag_emoji' => '🇺🇾',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 12,
            ],
            [
                'code' => 'VES',
                'name' => 'Venezuelan Bolívar',
                'symbol' => 'Bs.',
                'flag_emoji' => '🇻🇪',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 13,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'CA$',
                'flag_emoji' => '🇨🇦',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 14,
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'flag_emoji' => '🇦🇺',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 15,
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'flag_emoji' => '🇨🇭',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 16,
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'flag_emoji' => '🇨🇳',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 17,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                array_merge($currency, ['id' => \Illuminate\Support\Str::uuid()])
            );
        }
    }
}
