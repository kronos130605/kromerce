<?php

namespace App\Jobs\Currency;

use App\Services\CurrencyRateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateCurrencyRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 600;

    public function __construct(
        private bool $force = false
    ) {}

    public function handle(CurrencyRateService $currencyService): void
    {
        try {
            Log::info('Starting currency rate update job', ['force' => $this->force]);

            $result = $currencyService->updateDailyRates();

            if ($result['success']) {
                Log::info('Currency rates updated successfully', [
                    'total_updated' => $result['total_updated'],
                    'global_rates' => $result['results']['global']['updated'] ?? 0,
                    'business_rates' => $result['results']['business']['updated'] ?? 0,
                ]);
            } else {
                Log::error('Currency rate update failed', [
                    'error' => $result['error'],
                ]);

                throw new \Exception("Currency update failed: {$result['error']}");
            }
        } catch (\Exception $e) {
            Log::error('Currency rate update job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateCurrencyRates job failed permanently', [
            'error' => $exception->getMessage(),
        ]);

        // Could dispatch alert to admin here
    }
}
