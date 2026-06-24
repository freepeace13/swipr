<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\DeletesMessages;
use App\Models\Chat\Message;
use App\Models\User;

class DeleteMessage implements DeletesMessages
{
    public function execute(Message $message, User $user): bool
    {
        if ($message->sender_id !== $user->id) {
            throw new \InvalidArgumentException('Only the sender can delete their message.');
        }

        return $message->delete();
    }
}
