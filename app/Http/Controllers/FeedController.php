<?php

namespace App\Http\Controllers;

use App\Services\MatchMakingService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request, MatchMakingService $matchmaking)
    {
        $user = $request->user()->load('interests');
        $matches = $matchmaking->paginate($user);

        if ($request->header('X-Feed-Page')) {
            return view('pages.feeds-panel', ['matches' => $matches]);
        }

        return view('pages.feeds', ['matches' => $matches]);
    }
}
