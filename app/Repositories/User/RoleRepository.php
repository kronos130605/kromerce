<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function getAllNames(): array
    {
        return $this->model::pluck('name')->toArray();
    }

    public function getRolePriorityList(): array
    {
        return [
            'owner' => 1,
            'business_owner' => 2,
            'admin' => 3,
            'manager' => 4,
            'employee' => 5,
            'customer' => 6,
        ];
    }

    public function getHighestPriorityRole(array $roles): ?string
    {
        $rolePriority = $this->getRolePriorityList();

        $selectedRole = null;
        $highestPriority = PHP_INT_MAX;

        foreach ($roles as $role) {
            if (isset($rolePriority[$role]) && $rolePriority[$role] < $highestPriority) {
                $selectedRole = $role;
                $highestPriority = $rolePriority[$role];
            }
        }

        return $selectedRole;
    }
}
