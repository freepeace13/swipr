<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\DeletesMessages;
use App\Contracts\Chat\SendsMessages;
use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class StoreMessage extends Controller
{
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        Conversation $conversation,
        Message $message,
        SendsMessages $action
    ) {
        $this->authorize('create', [Message::class, $conversation]);

        $message = $action->send($request->user(), $conversation, $request->all());

        return response()->json([
            'message' => $message->load('sender'),
        ], 201);
    }
}
