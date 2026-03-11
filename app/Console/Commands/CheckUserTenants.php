<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserTenants extends Command
{
    protected $signature = 'user:check-tenants {id}';
    protected $description = 'Check user tenants';

    public function handle()
    {
        $userId = $this->argument('id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $this->info("User ID: {$user->id}");
        $this->info("Email: {$user->email}");
        $this->info("Tenants count: " . $user->tenants()->count());
        
        $tenants = $user->tenants;
        foreach ($tenants as $tenant) {
            $this->info("  - Tenant ID: {$tenant->id}, Name: {$tenant->name}");
        }
        
        return 0;
    }
}
