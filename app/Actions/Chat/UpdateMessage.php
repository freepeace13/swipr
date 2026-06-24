<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\UpdatesMessages;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UpdateMessage implements UpdatesMessages
{
    public function update(User $user, Message $message, array $input)
    {
        $validated = Validator::make($input, [
            //
        ])->validateWithBag('updateMessage');

        //
    }
}
