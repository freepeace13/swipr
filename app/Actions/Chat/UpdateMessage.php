<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\UpdatesMessages;
use App\Events\Chat\MessageUpdated;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UpdateMessage implements UpdatesMessages
{
    public function update(User $user, Message $message, array $input): Message
    {
        $validated = Validator::make($input, [
            'body' => ['required', 'string', 'max:5000'],
        ])->validateWithBag('updateMessage');

        if ($message->sender_id !== $user->id) {
            throw new \InvalidArgumentException('Only the sender can edit their message.');
        }

        $message->update(['body' => $validated['body']]);

        broadcast(new MessageUpdated($message))->toOthers();

        return $message;
    }
}
