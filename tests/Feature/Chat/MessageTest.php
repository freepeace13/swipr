<?php

namespace Tests\Feature\Chat;

use App\Actions\Chat\SendMessage;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_participant_can_send_a_message(): void
    {
        $conversation = Conversation::factory()->create();

        $this->actingAs($conversation->sender)
            ->postJson(route('chat.conversations.messages.store', $conversation), [
                'body' => 'Hello there',
            ])
            ->assertCreated()
            ->assertJsonPath('message.body', 'Hello there');

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $conversation->sender_id,
            'body' => 'Hello there',
        ]);
    }

    #[Test]
    public function sending_a_message_requires_a_body(): void
    {
        $conversation = Conversation::factory()->create();

        $this->actingAs($conversation->sender)
            ->from(route('chat.conversations.show', $conversation))
            ->post(route('chat.conversations.messages.store', $conversation), [])
            ->assertRedirect(route('chat.conversations.show', $conversation))
            ->assertSessionHasErrors('body', errorBag: 'sendMessage');

        $this->assertDatabaseEmpty('chat_messages');
    }

    #[Test]
    public function the_sender_can_edit_their_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Original']);

        $this->actingAs($conversation->sender)
            ->patchJson(
                route('chat.conversations.messages.update', [$conversation, $message]),
                ['body' => 'Edited']
            )
            ->assertOk()
            ->assertJsonPath('message.body', 'Edited');

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'body' => 'Edited',
        ]);
    }

    #[Test]
    public function the_sender_can_delete_their_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Delete me']);

        $this->actingAs($conversation->sender)
            ->delete(route('chat.conversations.messages.destroy', [$conversation, $message]))
            ->assertRedirect();

        $this->assertSoftDeleted('chat_messages', ['id' => $message->id]);
    }
}
