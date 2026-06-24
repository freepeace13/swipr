<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\DeletesConversations;
use App\Models\Chat\Conversation;
use App\Models\User;

class DeleteConversation implements DeletesConversations
{
    public function execute(Conversation $conversation, User $user): bool
    {
        if ($conversation->sender_id === $user->id) {
            return $conversation->update(['sender_deleted_at' => now()]);
        }

        if ($conversation->recipient_id === $user->id) {
            return $conversation->update(['recipient_deleted_at' => now()]);
        }

        throw new \InvalidArgumentException('User is not a participant in this conversation.');
    }
}
