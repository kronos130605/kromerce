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
            $businessRoles = config('roles.business_roles', ['business_owner']);

            if (!$user->hasAnyRole($businessRoles)) {
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
