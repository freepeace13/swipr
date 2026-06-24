<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShowConversation extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = $request->user();

        $deletedAt = $conversation->sender_id === $user->id
            ? $conversation->sender_deleted_at
            : $conversation->recipient_deleted_at;

        $conversation->load(['sender', 'recipient']);

        $conversation->setRelation(
            'messages',
            $conversation->messages()
                ->when($deletedAt, fn ($q) => $q->where('created_at', '>', $deletedAt))
                ->with(['sender', 'status'])
                ->get()
        );

        return view('pages.chat.conversation', compact('conversation'));
    }
}
