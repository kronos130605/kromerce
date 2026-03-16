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

        // Special handling for 'business' role - check if user has any business role in current store
        if ($role === 'business_owner') {

            // Get store from session instead of tenant()
            $storeId = session('current_store_id');

            if (!$storeId) {
                // Try to get store from user
                $store = $user->currentStore() ?: $user->stores()->first();
                if ($store) {
                    $storeId = $store->id;
                    session(['current_store_id' => $storeId]);
                }
            }

            if (!$storeId) {
                abort(403, 'Business access requires store');
            }

            // Get business roles from config
            $businessRoles = config('roles.store_management_roles', ['business_owner']);
            $userRoleInStore = $user->stores()
                ->where('stores.id', $storeId)
                ->whereIn('store_users.role', $businessRoles)
                ->exists();

            if (!$userRoleInStore) {
                abort(403, 'Unauthorized - Business role required');
            }
        } else {
            // For other roles, use standard hasRole check
            if (!$user->hasRole($role)) {
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
