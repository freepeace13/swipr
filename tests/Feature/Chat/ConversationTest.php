<?php

namespace Tests\Feature\Chat;

use App\Actions\Chat\SendMessage;
use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_from_the_inbox(): void
    {
        $this->get(route('chat.inbox'))->assertRedirect('/');
    }

    #[Test]
    public function a_user_can_view_their_inbox(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('chat.inbox'))
            ->assertOk();
    }

    #[Test]
    public function a_user_can_start_a_conversation_with_another_user(): void
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();

        $this->actingAs($user)
            ->post(route('chat.conversations.store'), ['recipient_id' => $recipient->id])
            ->assertRedirect();

        $this->assertDatabaseHas('chat_conversations', [
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
        ]);
    }

    #[Test]
    public function starting_a_conversation_reuses_an_existing_one(): void
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();
        $existing = Conversation::factory()->create([
            'sender_id' => $user->id,
            'recipient_id' => $recipient->id,
        ]);

        $this->actingAs($user)
            ->post(route('chat.conversations.store'), ['recipient_id' => $recipient->id])
            ->assertRedirect(route('chat.conversations.show', $existing));

        $this->assertSame(1, Conversation::count());
    }

    #[Test]
    public function a_participant_can_view_a_conversation(): void
    {
        $conversation = Conversation::factory()->create();

        $this->actingAs($conversation->sender)
            ->get(route('chat.conversations.show', $conversation))
            ->assertOk();
    }

    #[Test]
    public function reading_a_conversation_marks_unread_messages_as_read(): void
    {
        $conversation = Conversation::factory()->create();
        (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Hi']);

        $this->actingAs($conversation->recipient)
            ->post(route('chat.conversations.read', $conversation))
            ->assertOk()
            ->assertJson(['read' => 1]);
    }

    #[Test]
    public function a_participant_can_delete_a_conversation_for_themselves(): void
    {
        $conversation = Conversation::factory()->create();

        $this->actingAs($conversation->sender)
            ->delete(route('chat.conversations.destroy', $conversation))
            ->assertOk();

        $this->assertNotNull($conversation->fresh()->sender_deleted_at);
        $this->assertNull($conversation->fresh()->recipient_deleted_at);
    }
}
