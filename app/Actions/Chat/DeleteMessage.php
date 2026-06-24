<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\DeletesMessages;
use App\Events\Chat\MessageDeleted;
use App\Models\Chat\Message;
use App\Models\User;

class DeleteMessage implements DeletesMessages
{
    public function delete(User $user, Message $message): bool
    {
        if ($message->sender_id !== $user->id) {
            throw new \InvalidArgumentException('Only the sender can delete their message.');
        }

        $conversationId = $message->conversation_id;
        $messageId = $message->id;

        $deleted = $message->delete();

        broadcast(new MessageDeleted($conversationId, $messageId))->toOthers();

        return $deleted;
    }
}
