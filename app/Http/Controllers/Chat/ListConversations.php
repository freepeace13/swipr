<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Http\Request;

class ListConversations extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::query()
            ->where(
                fn ($query) => $query
                    ->where('sender_id', $user->getKey())
                    ->where(fn ($q) => $q
                        ->whereNull('sender_deleted_at')
                        ->orWhereColumn('last_message_at', '>', 'sender_deleted_at')
                    )
            )
            ->orWhere(
                fn ($query) => $query
                    ->where('recipient_id', $user->getKey())
                    ->where(fn ($q) => $q
                        ->whereNull('recipient_deleted_at')
                        ->orWhereColumn('last_message_at', '>', 'recipient_deleted_at')
                    )
            )
            ->orderByDesc('last_message_at')
            ->with(['lastMessage', 'sender', 'recipient'])
            ->paginate(20);

        return view('pages.chat.inbox', compact('conversations'));
    }
}
