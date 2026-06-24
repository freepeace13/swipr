<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\ReadsConversations;
use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ReadConversation extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Conversation $conversation, ReadsConversations $action)
    {
        $this->authorize('view', $conversation);

        $read = $action->read($request->user(), $conversation);

        return response()->json(['read' => $read]);
    }
}
