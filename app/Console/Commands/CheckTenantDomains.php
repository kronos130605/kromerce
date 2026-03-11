<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class CheckTenantDomains extends Command
{
    protected $signature = 'tenant:check-domains {id}';
    protected $description = 'Check tenant domains';

    public function handle()
    {
        $tenantId = $this->argument('id');
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return 1;
        }
        
        $this->info("Tenant ID: {$tenant->id}");
        $this->info("Tenant Name: {$tenant->name}");
        $this->info("Domains count: " . $tenant->domains()->count());
        
        $domains = $tenant->domains;
        foreach ($domains as $domain) {
            $this->info("  - Domain: {$domain->domain}");
        }
        
        return 0;
    }
}
