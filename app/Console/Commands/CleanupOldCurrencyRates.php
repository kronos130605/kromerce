<?php

namespace App\Console\Commands;

use App\Models\CurrencyRateGlobal;
use App\Models\CurrencyRateBusiness;
use App\Models\BusinessCurrencyConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old currency rates based on retention policies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN - Showing what would be deleted...');
        } else {
            $this->info('🧹 Cleaning up old currency rates...');
        }

        try {
            $totalDeleted = 0;

            // Clean global rates (keep 2 years)
            $globalCutoff = now()->subYears(2)->format('Y-m-d');
            $globalCount = CurrencyRateGlobal::where('effective_date', '<', $globalCutoff)->count();
            
            if ($globalCount > 0) {
                $this->info("Global rates older than {$globalCutoff}: {$globalCount} records");
                
                if (!$isDryRun) {
                    $deleted = CurrencyRateGlobal::where('effective_date', '<', $globalCutoff)->delete();
                    $totalDeleted += $deleted;
                    $this->info("✅ Deleted {$deleted} global rate records");
                }
            }

            // Clean business rates based on their retention settings
            $configs = BusinessCurrencyConfig::all();
            
            foreach ($configs as $config) {
                $businessCutoff = now()->subYears($config->historical_retention_years)->format('Y-m-d');
                $businessCount = CurrencyRateBusiness::where('tenant_id', $config->tenant_id)
                    ->where('effective_date', '<', $businessCutoff)
                    ->count();
                
                if ($businessCount > 0) {
                    $this->info("Business {$config->tenant->name} ({$config->historical_retention_years} years): {$businessCount} records");
                    
                    if (!$isDryRun) {
                        $deleted = CurrencyRateBusiness::where('tenant_id', $config->tenant_id)
                            ->where('effective_date', '<', $businessCutoff)
                            ->delete();
                        $totalDeleted += $deleted;
                        $this->info("✅ Deleted {$deleted} rate records for {$config->tenant->name}");
                    }
                }
            }

            if ($isDryRun) {
                $this->info("🔍 DRY RUN COMPLETE - Would delete {$totalDeleted} total records");
                $this->info("Run without --dry-run to actually delete the records");
            } else {
                $this->info("✅ CLEANUP COMPLETE - Deleted {$totalDeleted} total records");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Cleanup failed: {$e->getMessage()}");
            Log::error('Currency cleanup command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return self::FAILURE;
        }
    }
}
