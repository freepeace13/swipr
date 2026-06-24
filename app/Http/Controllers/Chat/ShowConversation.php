<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ShowConversation extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->loadMissing([
            'sender',
            'recipient',
            'messages.sender',
            'messages.status',
        ]);

        return view('pages.chat.conversation', compact('conversation'));
    }
}
