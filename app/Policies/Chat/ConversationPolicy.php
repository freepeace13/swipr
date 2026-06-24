<?php

namespace App\Policies\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return true;
    }

    public function delete(User $user, Conversation $conversation): bool
    {
        return true;
    }
}
