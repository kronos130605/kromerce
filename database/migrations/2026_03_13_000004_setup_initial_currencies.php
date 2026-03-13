<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Setup initial currency rates for USD, EUR, CUP
        if (Schema::hasTable('currency_rates_global')) {
            $today = now()->toDateString();
            
            // Base rates (these should be updated with real rates)
            $baseRates = [
                // USD to other currencies
                ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => 0.92, 'effective_date' => $today],
                ['from_currency' => 'USD', 'to_currency' => 'CUP', 'rate' => 120.00, 'effective_date' => $today],
                
                // EUR to other currencies
                ['from_currency' => 'EUR', 'to_currency' => 'USD', 'rate' => 1.09, 'effective_date' => $today],
                ['from_currency' => 'EUR', 'to_currency' => 'CUP', 'rate' => 130.00, 'effective_date' => $today],
                
                // CUP to other currencies
                ['from_currency' => 'CUP', 'to_currency' => 'USD', 'rate' => 0.0083, 'effective_date' => $today],
                ['from_currency' => 'CUP', 'to_currency' => 'EUR', 'rate' => 0.0077, 'effective_date' => $today],
                
                // Self rates (1:1)
                ['from_currency' => 'USD', 'to_currency' => 'USD', 'rate' => 1.0, 'effective_date' => $today],
                ['from_currency' => 'EUR', 'to_currency' => 'EUR', 'rate' => 1.0, 'effective_date' => $today],
                ['from_currency' => 'CUP', 'to_currency' => 'CUP', 'rate' => 1.0, 'effective_date' => $today],
            ];
            
            foreach ($baseRates as $rate) {
                DB::table('currency_rates_global')->updateOrInsert(
                    [
                        'from_currency' => $rate['from_currency'],
                        'to_currency' => $rate['to_currency'],
                        'effective_date' => $rate['effective_date']
                    ],
                    [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'rate' => $rate['rate'],
                        'source' => 'manual_setup',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        // Create currency rate update log
        if (Schema::hasTable('currency_rate_updates')) {
            DB::table('currency_rate_updates')->insert([
                'update_date' => now()->toDateString(),
                'currencies_updated' => json_encode([
                    'USD-EUR', 'USD-CUP', 'EUR-USD', 'EUR-CUP', 
                    'CUP-USD', 'CUP-EUR', 'USD-USD', 'EUR-EUR', 'CUP-CUP'
                ]),
                'source' => 'manual_setup',
                'success' => true,
                'error_message' => null,
                'total_rates_updated' => 9,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the initial currency rates
        if (Schema::hasTable('currency_rates_global')) {
            $today = now()->toDateString();
            
            DB::table('currency_rates_global')
                ->where('effective_date', $today)
                ->where('source', 'manual_setup')
                ->delete();
        }
        
        // Remove the update log entry
        if (Schema::hasTable('currency_rate_updates')) {
            DB::table('currency_rate_updates')
                ->where('update_date', now()->toDateString())
                ->where('source', 'manual_setup')
                ->delete();
        }
    }
};
