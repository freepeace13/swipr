<?php

namespace App\Policies\Chat;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;

class MessagePolicy
{
    public function create(User $user, Conversation $conversation): bool
    {
        return $conversation->isParticipant($user);
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->getKey() === $message->sender_id;
    }

    public function update(User $user, Message $message): bool
    {
        return $user->getKey() === $message->sender_id;
    }
}
