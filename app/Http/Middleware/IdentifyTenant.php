<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hostname = $request->getHost();

        // Skip tenant identification for admin routes
        if ($request->is('admin/*') || $request->is('register') || $request->is('login')) {
            return $next($request);
        }

        // Check if this is a central domain - if so, get tenant from authenticated user
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($hostname, $centralDomains)) {
            $user = $request->user();
            if ($user) {
                // Get tenant from user's current tenant or first tenant
                $tenant = $user->currentTenant() ?: $user->tenants()->first();

                if ($tenant) {
                    tenancy()->initialize($tenant);
                    view()->share('current_tenant', $tenant);

                    return $next($request);
                }
            }
        }

        // Try to find tenant by domain (for subdomain tenants)
        $tenant = Tenant::whereHas('domains', function ($query) use ($hostname) {
            $query->where('domain', $hostname);
        })->first();

        if ($tenant) {
            // Initialize tenancy
            tenancy()->initialize($tenant);

            // Make tenant available in views
            view()->share('current_tenant', $tenant);
        } else {
            Log::warning('IdentifyTenant - No tenant found for hostname', ['hostname' => $hostname]);
        }

        return $next($request);
    }
}
