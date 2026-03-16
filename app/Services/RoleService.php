<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\User\RoleRepository;
use Illuminate\Support\Facades\Log;

class RoleService
{
    private RoleRepository $roleRepository;

    public function __construct(
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get all available roles
     */
    public function getAvailableRoles(): array
    {
        try {
            return $this->roleRepository->getAllNames();
        } catch (\Exception $e) {
            Log::error('Error getting available roles', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get user's primary role from Laravel permissions
     */
    public function getUserPrimaryRole(User $user): ?string
    {
        try {
            // Get the first role from Laravel's Spatie Permission package
            $roles = $user->getRoleNames();
            
            if ($roles->isEmpty()) {
                return null;
            }

            // Get role priority from config
            $rolePriority = config('roles.role_priority');

            // Find the role with highest priority (lowest number)
            $primaryRole = null;
            $highestPriority = PHP_INT_MAX;

            foreach ($roles as $role) {
                $priority = $rolePriority[$role] ?? 999;
                if ($priority < $highestPriority) {
                    $highestPriority = $priority;
                    $primaryRole = $role;
                }
            }

            return $primaryRole;
        } catch (\Exception $e) {
            Log::error('Error getting user primary role', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
