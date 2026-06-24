<?php

namespace App\Policies\Chat;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user, Conversation $conversation): bool
    {
        return true;
    }

    public function delete(User $user, Message $message): bool
    {
        return true;
    }

    public function update(User $user, Message $message): bool
    {
        return true;
    }
}
