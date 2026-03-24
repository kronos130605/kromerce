<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        return null;
    }

    public function view(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id
            || $store->users()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();
    }

    public function manage(User $user, Store $store): bool
    {
        if ($store->owner_id === $user->id) {
            return true;
        }

        return $user->hasRole(['business_owner', 'manager'])
            && $store->users()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();
    }

    public function update(User $user, Store $store): bool
    {
        return $this->manage($user, $store);
    }

    public function delete(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }
}
