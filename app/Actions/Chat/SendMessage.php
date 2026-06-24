<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\SendsMessages;
use App\Enums\Chat\MessageType;
use App\Events\Chat\MessageSent;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SendMessage implements SendsMessages
{
    public function send(User $sender, Conversation $conversation, array $input): Message
    {
        $validated = Validator::make($input, [
            'body' => ['required', 'string', 'max:5000'],
        ])->validateWithBag('sendMessage');

        if (! $conversation->isParticipant($sender)) {
            throw new \InvalidArgumentException('Sender is not a participant in this conversation.');
        }

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'body' => $validated['body'],
            'type' => MessageType::Text,
        ]);

        $message->status()->create([
            'recipient_id' => $conversation->otherParticipantId($sender),
        ]);

        $updates = ['last_message_at' => $message->created_at];

        if ($conversation->sender_id === $sender->id && $conversation->sender_deleted_at) {
            $updates['sender_deleted_at'] = null;
        } elseif ($conversation->recipient_id === $sender->id && $conversation->recipient_deleted_at) {
            $updates['recipient_deleted_at'] = null;
        }

        $conversation->update($updates);

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }
}
