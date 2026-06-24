<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;

interface SendsMessages
{
    public function send(User $sender, Conversation $conversation, array $input): Message;
}
