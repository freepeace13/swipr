<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, User $profile): bool
    {
        return true;
    }

    public function edit(User $user, User $profile): bool
    {
        return $user->is($profile);
    }

    public function update(User $user, User $profile): bool
    {
        return $user->is($profile);
    }
}
