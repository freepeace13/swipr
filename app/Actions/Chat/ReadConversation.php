<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\ReadsConversations;
use App\Models\Chat\Conversation;
use App\Models\Chat\MessageStatus;
use App\Models\User;

class ReadConversation implements ReadsConversations
{
    public function execute(Conversation $conversation, User $user): int
    {
        $now = now();

        return MessageStatus::whereHas('message', fn ($q) => $q->where('conversation_id', $conversation->id))
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => $now,
                'delivered_at' => $now,
            ]);
    }
}
