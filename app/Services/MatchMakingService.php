<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;

class MatchMakingService
{
    public function get(User $user, int $limit = 20): Collection
    {
        return User::query()
            ->where('users.id', '!=', $user->id)
            ->matchesByGender($user)
            ->matchesByAge($user)
            ->rankedByInterestMatch($user)
            ->with('interests')
            ->limit($limit)
            ->get();
    }

    public function paginate(User $user, int $perPage = 10): CursorPaginator
    {
        return User::query()
            ->where('users.id', '!=', $user->id)
            ->matchesByGender($user)
            ->matchesByAge($user)
            ->with('interests')
            ->orderBy('users.id')
            ->cursorPaginate($perPage);
    }
}
