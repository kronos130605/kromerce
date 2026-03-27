<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\StoreService;

class IdentifyStore
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip store identification for admin routes
        if ($request->is('admin/*') || $request->is('register') || $request->is('login')) {
            return $next($request);
        }

        $storeService = app(StoreService::class);
        $store = $storeService->resolveCurrentStoreForRequest($request);

        if ($store) {
            view()->share('current_store', $store);
        }

        // No store found - continue without tenancy
        return $next($request);
    }
}
