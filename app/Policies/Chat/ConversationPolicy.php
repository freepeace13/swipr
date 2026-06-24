<?php

namespace App\Policies\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->isParticipant($user);
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->isParticipant($user);
    }
}
