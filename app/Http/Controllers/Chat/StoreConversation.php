<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\CreatesConversations;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreConversation extends Controller
{
    public function __invoke(Request $request, CreatesConversations $creator)
    {
        $conversation = $creator->create($request->user(), $request->all());

        return redirect()->route('chat.conversations.show', compact('conversation'));
    }
}
