<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
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
