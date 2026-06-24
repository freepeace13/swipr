<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;

interface CreatesConversations
{
    public function create(User $sender, array $input): Conversation;
}
