<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\DeletesConversations;
use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DestroyConversation extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Conversation $conversation, DeletesConversations $action)
    {
        $this->authorize('delete', $conversation);

        $action->delete($request->user(), $conversation);

        return redirect()->route('chat.inbox');
    }
}
