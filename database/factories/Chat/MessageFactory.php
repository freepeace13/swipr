<?php

namespace Database\Factories\Chat;

use App\Enums\Chat\MessageType;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Message> */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => fn (array $attributes) => Conversation::find($attributes['conversation_id'])->sender_id,
            'body' => fake()->sentence(),
            'type' => MessageType::Text,
        ];
    }
}
