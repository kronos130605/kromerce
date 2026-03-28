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
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'flag_emoji' => '🇪🇺',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CUP',
                'name' => 'Cuban Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇺',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'flag_emoji' => '🇬🇧',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'flag_emoji' => '🇯🇵',
                'decimal_places' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'MXN',
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'flag_emoji' => '🇲🇽',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'COP',
                'name' => 'Colombian Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇴',
                'decimal_places' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'ARS',
                'name' => 'Argentine Peso',
                'symbol' => '$',
                'flag_emoji' => '🇦🇷',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'BRL',
                'name' => 'Brazilian Real',
                'symbol' => 'R$',
                'flag_emoji' => '🇧🇷',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'PEN',
                'name' => 'Peruvian Sol',
                'symbol' => 'S/',
                'flag_emoji' => '🇵🇪',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CLP',
                'name' => 'Chilean Peso',
                'symbol' => '$',
                'flag_emoji' => '🇨🇱',
                'decimal_places' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'UYU',
                'name' => 'Uruguayan Peso',
                'symbol' => '$U',
                'flag_emoji' => '🇺🇾',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'VES',
                'name' => 'Venezuelan Bolívar',
                'symbol' => 'Bs.',
                'flag_emoji' => '🇻🇪',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'CA$',
                'flag_emoji' => '🇨🇦',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'flag_emoji' => '🇦🇺',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'flag_emoji' => '🇨🇭',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'flag_emoji' => '🇨🇳',
                'decimal_places' => 2,
                'is_active' => true
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                array_merge($currency)
            );
        }
    }
}
