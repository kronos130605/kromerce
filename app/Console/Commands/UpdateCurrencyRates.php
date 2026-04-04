<?php

namespace App\Console\Commands;

use App\Jobs\Currency\UpdateCurrencyRates as UpdateCurrencyRatesJob;
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
            // Dispatch job instead of running synchronously
            UpdateCurrencyRatesJob::dispatch($this->option('force'));

            $this->info('✅ Currency rate update job dispatched successfully!');
            $this->info('The rates will be updated in the background.');

            return self::SUCCESS;
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
