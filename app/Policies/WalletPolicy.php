<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        return null;
    }

    public function view(User $user, Store $store): bool
    {
        return $store->user_id === $user->id;
    }

    public function withdraw(User $user, Store $store): bool
    {
        return $store->user_id === $user->id && $store->status === 'approved';
    }
}
