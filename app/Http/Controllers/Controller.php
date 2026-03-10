<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Tenant;
use App\Traits\ApiResponse;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;

    /**
     * Validate tenant context.
     */
    protected function validateTenant(Tenant $tenant = null): ?Tenant
    {
        if (!$tenant) {
            $tenant = tenant();
        }

        if (!$tenant) {
            \Log::error('validateTenant: no tenant found, throwing exception');
            throw new \Exception('No tenant context found');
        }

        \Log::info('validateTenant success', [
            'tenant_id' => $tenant->id,
        ]);

        return $tenant;
    }

    /**
     * Validate user access to tenant.
     */
    protected function validateUserTenantAccess(\App\Models\User $user, Tenant $tenant): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->tenants()->where('tenants.id', $tenant->id)->exists();
    }
}
