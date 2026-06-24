<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;

interface DeletesConversations
{
    public function delete(User $user, Conversation $conversation): bool;
}
