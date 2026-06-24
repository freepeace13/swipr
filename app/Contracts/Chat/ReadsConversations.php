<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;

interface ReadsConversations
{
    public function read(User $user, Conversation $conversation): int;
}
