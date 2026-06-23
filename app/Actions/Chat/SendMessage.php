<?php

namespace App\Actions\Chat;

use App\Enums\Chat\MessageType;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;

class SendMessage
{
    public function execute(Conversation $conversation, User $sender, string $body): Message
    {
        if (! $conversation->isParticipant($sender)) {
            throw new \InvalidArgumentException('Sender is not a participant in this conversation.');
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'body' => $body,
            'type' => MessageType::Text,
        ]);

        $message->status()->create([
            'recipient_id' => $conversation->otherParticipantId($sender),
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        return $message;
    }
}
