<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\CreatesConversations;
use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateConversation implements CreatesConversations
{
    public function create(User $sender, array $input): Conversation
    {
        $validated = Validator::make($input, [
            'recipient_id' => ['required', 'exists:users,id'],
        ])->validateWithBag('createConversation');

        $recipientId = $validated['recipient_id'];
        $senderId = $sender->getKey();

        if ($recipientId === $senderId) {
            throw new \InvalidArgumentException('Cannot create a conversation with yourself.');
        }

        $record = Conversation::query()
            ->betweenParticipants($senderId, $recipientId)
            ->first();

        if (!$record) {
            $record = Conversation::create([
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
            ]);
        }

        return $record;
    }
}
