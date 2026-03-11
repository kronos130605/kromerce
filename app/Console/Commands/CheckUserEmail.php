<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserEmail extends Command
{
    protected $signature = 'user:check-email {id}';
    protected $description = 'Check user email verification status';

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
        $this->info("Email Verified: " . ($user->email_verified_at ? 'YES' : 'NO'));
        $this->info("Email Verified At: " . ($user->email_verified_at ?? 'NULL'));
        
        return 0;
    }
}
