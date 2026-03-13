<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Store;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;

    /**
     * Validate store context.
     */
    protected function validateStore(Store $store = null): ?Store
    {
        if (!$store) {
            $store = tenant();
        }

        if (!$store) {
            Log::error('validateStore: no store found, throwing exception');
            throw new \Exception('No store context found');
        }

        return $store;
    }

    /**
     * Validate user access to store.
     */
    protected function validateUserStoreAccess(\App\Models\User $user, Store $store): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->stores()->where('stores.id', $store->id)->exists();
    }
}
