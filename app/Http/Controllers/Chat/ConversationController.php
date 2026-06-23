<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __invoke(Conversation $conversation)
    {
        $conversation->loadMissing(['messages']);

        return view('pages.chat.conversation', compact('conversation'));
    }
}
