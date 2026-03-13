<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class IdentifyStore
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hostname = $request->getHost();

        // Skip store identification for admin routes
        if ($request->is('admin/*') || $request->is('register') || $request->is('login')) {
            return $next($request);
        }

        // Check if this is a central domain - if so, get store from authenticated user
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($hostname, $centralDomains)) {
            $user = $request->user();

            if ($user) {
                // Get store from user's current store or first store
                $store = $user->currentStore() ?: $user->stores()->first();

                if ($store) {
                    tenancy()->initialize($store);
                    view()->share('current_store', $store);

                    return $next($request);
                }
            }
        }

        // Try to find store by domain (for subdomain stores)
        $store = Store::whereHas('domains', function ($query) use ($hostname) {
            $query->where('domain', $hostname);
        })->first();

        if ($store) {
            // Initialize tenancy
            tenancy()->initialize($store);
            view()->share('current_store', $store);

            return $next($request);
        }

        // No store found - continue without tenancy
        return $next($request);
    }
}
