<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Special handling for 'business' role - check if user has any business role in current tenant
        if ($role === 'business') {
            $tenant = tenant();
            if (!$tenant) {
                abort(403, 'Business access requires tenant');
            }
            
            // Check if user has any business role in the current tenant using tenant_users table
            $businessRoles = ['business_owner', 'owner', 'admin', 'manager', 'employee'];
            $userRoleInTenant = $user->tenants()
                ->where('tenants.id', $tenant->id)
                ->whereIn('tenant_users.role', $businessRoles)
                ->exists();
                
            if (!$userRoleInTenant) {
                abort(403, 'Unauthorized - Business role required');
            }
        } else {
            // For other roles, use the standard hasRole check
            if (!$user->hasRole($role)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
