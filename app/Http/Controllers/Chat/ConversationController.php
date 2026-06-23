<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\DeleteConversationRequest;
use App\Http\Requests\Chat\StoreConversationRequest;
use App\Models\Chat\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function show(Conversation $conversation)
    {
        $conversation->loadMissing(['messages']);

        return view('pages.chat.conversation', compact('conversation'));
    }

    public function store(StoreConversationRequest $request)
    {
        //
    }

    public function destroy(DeleteConversationRequest $request)
    {
        //
    }
}
