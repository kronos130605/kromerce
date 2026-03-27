<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRoleRepository
{
    /**
     * Get user roles from Spatie
     */
    public function getSpatieRoles(User $user): Collection
    {
        try {
            return $user->roles()->get();
        } catch (\Exception $e) {
            Log::error('Error getting Spatie roles', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Get user role names from Spatie
     */
    public function getSpatieRoleNames(User $user): array
    {
        try {
            return $user->roles->pluck('name')->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting Spatie role names', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get available roles from Spatie
     */
    public function getAvailableRoles(): array
    {
        try {
            return DB::table('roles')
                ->pluck('name')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available roles', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
