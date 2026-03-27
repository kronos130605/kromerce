<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['business_owner', 'manager']);
    }

    public function view(User $user, Product $product): bool
    {
        if (!$product->store) {
            return false;
        }

        return $user->can('view', $product->store);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['business_owner', 'manager']);
    }

    public function update(User $user, Product $product): bool
    {
        if (!$product->store) {
            return false;
        }

        return $user->can('manage', $product->store);
    }

    public function delete(User $user, Product $product): bool
    {
        if (!$product->store) {
            return false;
        }

        return $user->can('manage', $product->store);
    }

    public function restore(User $user, Product $product): bool
    {
        if (!$product->store) {
            return false;
        }

        return $user->can('manage', $product->store);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        if (!$product->store) {
            return false;
        }

        return $user->can('delete', $product->store);
    }
}
