<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Chat\DeletesMessages;
use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DestroyMessage extends Controller
{
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        Conversation $conversation,
        Message $message,
        DeletesMessages $action
    ) {
        $this->authorize('delete', $message);

        $action->delete($request->user(), $message);

        return back();
    }
}
