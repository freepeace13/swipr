<?php

namespace App\Actions\Chat;

use App\Contracts\Chat\ReadsMessages;
use App\Models\Chat\Message;
use App\Models\Chat\MessageStatus;
use App\Models\User;

class ReadMessage implements ReadsMessages
{
    public function execute(Message $message, User $recipient): MessageStatus
    {
        $status = MessageStatus::firstOrNew([
            'message_id' => $message->id,
            'recipient_id' => $recipient->id,
        ]);

        $now = now();
        $status->delivered_at ??= $now;
        $status->read_at = $now;
        $status->save();

        return $status;
    }
}
