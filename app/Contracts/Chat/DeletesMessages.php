<?php

namespace App\Contracts\Chat;

use App\Models\Chat\Message;
use App\Models\User;

interface DeletesMessages
{
    public function delete(User $user, Message $message): bool;
}
