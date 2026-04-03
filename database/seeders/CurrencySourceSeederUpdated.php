<?php

namespace Database\Seeders;

use App\Models\CurrencySource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class CurrencySourceSeederUpdated extends Seeder
{
    /**
     * Run the database seeds.
     * Three-tier architecture for Cuba operations:
     * 1. openexchange-global: International conversions (non-CUP)
     * 2. bcc-cuba: CUP official rates
     * 3. eltoque-cuba: CUP informal rates
     */
    public function run(): void
    {
        $sources = [
            // Tier 1: International Global Source
            // Used for all conversions NOT involving CUP/CLA
            [
                'name' => 'OpenExchangeRates - Global',
                'code' => 'openexchange-global',
                'type' => 'api',
                'provider_class' => 'App\Services\Currency\Providers\ApiCurrencyProvider',
                'is_active' => true,
                'is_global_default' => true,
                'base_url' => 'https://openexchangerates.org/api',
                'auth_type' => 'api_key',
                'config' => [
                    'rates_endpoint' => '/latest.json',
                    'test_endpoint' => '/latest.json',
                    'response_format' => 'openexchange',
                    'api_key_location' => 'query',
                    'api_key_name' => 'app_id',
                    'base_currency' => 'USD',
                    'currencies_supported' => ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY', 'INR', 'COP', 'MXN', 'BRL', 'ARS'],
                    'scope' => 'international',
                    'timeout' => 30,
                ],
                'default_credentials' => null,
            ],

            // Tier 2: Cuba Official - Banco Central de Cuba
            [
                'name' => 'Banco Central de Cuba - Oficial',
                'code' => 'bcc-cuba',
                'type' => 'api',
                'provider_class' => 'App\Services\Currency\Providers\BccCubaProvider',
                'is_active' => true,
                'is_global_default' => false,
                'base_url' => 'https://api.bc.gob.cu',
                'auth_type' => 'none',
                'config' => [
                    'rates_endpoint' => '/v1/tasas-de-cambio/activas',
                    'historical_endpoint' => '/v1/tasas-de-cambio/activas-por-fecha',
                    'test_endpoint' => '/v1/tasas-de-cambio/activas',
                    'response_format' => 'bcc-cuba',
                    'base_currency' => 'CUP',
                    'currencies_supported' => ['CUP', 'USD', 'EUR', 'MLC'],
                    'scope' => 'cuba-official',
                    'timeout' => 30,
                    'cla_equals_usd' => true,
                ],
                'default_credentials' => null,
            ],

            // Tier 3: Cuba Informal - ElToque
            [
                'name' => 'ElToque - Mercado Informal Cuba',
                'code' => 'eltoque-cuba',
                'type' => 'web',
                'provider_class' => 'App\Services\Currency\Providers\ElToqueProvider',
                'is_active' => true,
                'is_global_default' => false,
                'base_url' => 'https://eltoque.com/tasas-de-cambio-cuba/mercado-informal',
                'auth_type' => 'none',
                'config' => [
                    'selectors' => [
                        'rates_container' => '.exchange-rates-container',
                        'usd' => '[data-currency="USD"] .rate-value',
                        'eur' => '[data-currency="EUR"] .rate-value',
                        'mlc' => '[data-currency="MLC"] .rate-value',
                        'last_update' => '.last-update-time',
                    ],
                    'base_currency' => 'CUP',
                    'currencies_supported' => ['CUP', 'USD', 'EUR', 'MLC'],
                    'scope' => 'cuba-informal',
                    'timeout' => 30,
                    'cla_equals_usd' => true,
                ],
                'default_credentials' => null,
            ],
        ];

        foreach ($sources as $sourceData) {
            if ($sourceData['default_credentials']) {
                $sourceData['default_credentials'] = Crypt::encryptString(
                    json_encode($sourceData['default_credentials'])
                );
            }

            // Generate UUID if creating new record
            $existing = CurrencySource::where('code', $sourceData['code'])->first();
            if (!$existing) {
                $sourceData['id'] = (string) \Illuminate\Support\Str::uuid();
            }

            CurrencySource::updateOrCreate(
                ['code' => $sourceData['code']],
                $sourceData
            );
        }

        $this->command->info('Currency sources seeded successfully!');
        $this->command->info('');
        $this->command->info('Architecture:');
        $this->command->info('  1. openexchange-global: International conversions (non-CUP)');
        $this->command->info('  2. bcc-cuba: CUP official rates (default for CUP conversions)');
        $this->command->info('  3. eltoque-cuba: CUP informal rates (optional store choice)');
        $this->command->info('');
        $this->command->info('CLA (Tarjeta Clasica) automatically uses USD rate');
    }
}
