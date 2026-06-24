<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Message;
use App\Models\Chat\MessageStatus;
use App\Models\User;

interface ReadsMessages
{
    public function read(User $recipient, Message $message): MessageStatus;
}
