<?php

namespace App\Console\Commands;

use App\Services\CurrencyRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-rates {--force : Force update even if already updated today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily currency rates from API';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyRateService $currencyService): int
    {
        $this->info('Starting currency rate update...');

        try {
            $result = $currencyService->updateDailyRates();

            if ($result['success']) {
                $this->info("✅ Currency rates updated successfully!");
                $this->info("Total rates updated: {$result['total_updated']}");
                
                if (isset($result['results']['global']['updated'])) {
                    $this->info("Global rates: {$result['results']['global']['updated']}");
                }
                
                if (isset($result['results']['business']['updated'])) {
                    $this->info("Business rates: {$result['results']['business']['updated']}");
                }

                return self::SUCCESS;
            } else {
                $this->error("❌ Currency rate update failed!");
                $this->error("Error: {$result['error']}");
                
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Unexpected error: {$e->getMessage()}");
            Log::error('Currency rate command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return self::FAILURE;
        }
    }
}
