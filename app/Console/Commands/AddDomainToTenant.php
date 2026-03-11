<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class AddDomainToTenant extends Command
{
    protected $signature = 'tenant:add-domain {id} {domain}';
    protected $description = 'Add domain to tenant';

    public function handle()
    {
        $tenantId = $this->argument('id');
        $domain = $this->argument('domain');
        
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return 1;
        }
        
        // Check if domain already exists
        $existingDomain = $tenant->domains()->where('domain', $domain)->first();
        if ($existingDomain) {
            $this->info("Domain {$domain} already exists for tenant {$tenant->name}");
            return 0;
        }
        
        // Add new domain
        $tenant->domains()->create(['domain' => $domain]);
        
        $this->info("Domain {$domain} added to tenant {$tenant->name}");
        return 0;
    }
}
