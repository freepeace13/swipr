<?php

use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.conversation.{conversation}', function (User $user, Conversation $conversation) {
    return $conversation->isParticipant($user);
});
