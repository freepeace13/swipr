<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\ReadsConversations;
use App\Events\Chat\MessagesRead;
use App\Models\Chat\Conversation;
use App\Models\Chat\MessageStatus;
use App\Models\User;

class ReadConversation implements ReadsConversations
{
    public function read(User $user, Conversation $conversation): int
    {
        $now = now();

        $updated = MessageStatus::whereHas('message', fn ($q) => $q->where('conversation_id', $conversation->id))
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => $now,
                'delivered_at' => $now,
            ]);

        if ($updated > 0) {
            broadcast(new MessagesRead($conversation, $user))->toOthers();
        }

        return $updated;
    }
}
