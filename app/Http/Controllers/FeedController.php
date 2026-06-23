<?php

namespace App\Http\Controllers;

use App\Services\MatchMakingService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request, MatchMakingService $matchmaking)
    {
        $user = $request->user()->load('interests');

        return view('pages.feeds', [
            'matches' => $matchmaking->get($user),
        ]);
    }
}
