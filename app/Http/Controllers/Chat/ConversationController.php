<?php

namespace App\Http\Controllers\Chat;

use App\Actions\Chat\CreateConversation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\DeleteConversationRequest;
use App\Http\Requests\Chat\StoreConversationRequest;
use App\Models\Chat\Conversation;
use App\Models\User;

class ConversationController extends Controller
{
    public function show(Conversation $conversation)
    {
        $conversation->loadMissing([
            'sender',
            'recipient',
            'messages.sender',
            'messages.status',
        ]);

        return view('pages.chat.conversation', compact('conversation'));
    }

    public function store(StoreConversationRequest $request, CreateConversation $createConversation)
    {
        $recipient = User::findOrFail($request->validated('recipient_id'));
        $conversation = $createConversation->execute($request->user(), $recipient);

        return redirect()->route('chat.conversations.show', compact('conversation'));
    }

    public function destroy(DeleteConversationRequest $request)
    {
        //
    }
}
