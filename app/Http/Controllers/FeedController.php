<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 10);

        $user->loadMissing(['interests']);

        $matches = User::query()
            ->where('users.id', '!=', $user->id)
            ->matchesByGender($user)
            ->matchesByAge($user)
            ->with('interests')
            ->orderBy('users.id')
            ->cursorPaginate($perPage);

        if ($request->header('X-Feed-Page')) {
            return view('pages.feeds-panel', ['matches' => $matches]);
        }

        return view('pages.feeds', ['matches' => $matches]);
    }
}
