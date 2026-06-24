<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\CreatesConversations;
use App\Models\Chat\Conversation;
use App\Models\User;

class CreateConversation implements CreatesConversations
{
    public function execute(User $sender, User $recipient): Conversation
    {
        if ($sender->id === $recipient->id) {
            throw new \InvalidArgumentException('Cannot create a conversation with yourself.');
        }

        return Conversation::between($sender, $recipient)
            ?? Conversation::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
            ]);
    }
}
