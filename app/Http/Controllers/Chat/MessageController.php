<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\DeleteMessageRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\UpdateMessageRequest;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(SendMessageRequest $request)
    {
        //
    }

    public function update(UpdateMessageRequest $request)
    {
        //
    }

    public function destroy(DeleteMessageRequest $request)
    {
        //
    }
}
