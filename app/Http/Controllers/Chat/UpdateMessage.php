<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\UpdatesMessages;
use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UpdateMessage extends Controller
{
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        Conversation $conversation,
        Message $message,
        UpdatesMessages $action
    ) {
        $this->authorize('update', $message);

        $action->update($request->user(), $message, $request->all());

        return response()->json([
            'message' => $message->fresh()->load('sender'),
        ]);
    }
}
