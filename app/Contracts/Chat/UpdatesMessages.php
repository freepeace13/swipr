<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Message;
use App\Models\User;

interface UpdatesMessages
{
    public function update(User $user, Message $message, array $input);
}
