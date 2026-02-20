<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        // Try to find tenant by domain
        $tenant = Tenant::whereHas('domains', function ($query) use ($hostname) {
            $query->where('domain', $hostname);
        })->first();

        if ($tenant) {
            // Initialize tenancy
            tenancy()->initialize($tenant);
            
            // Make tenant available in views
            view()->share('current_tenant', $tenant);
        }

        return $next($request);
    }
}
