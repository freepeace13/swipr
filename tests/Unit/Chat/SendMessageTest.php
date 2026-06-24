<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\SendMessage;
use App\Enums\Chat\MessageType;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendMessageTest extends TestCase
{
    use RefreshDatabase;

    private SendMessage $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SendMessage;
    }

    #[Test]
    public function it_sends_a_text_message(): void
    {
        $conversation = Conversation::factory()->create();

        $message = $this->action->send($conversation->sender, $conversation, ['body' => 'Hello!']);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Hello!', $message->body);
        $this->assertEquals(MessageType::Text, $message->type);
        $this->assertEquals($conversation->id, $message->conversation_id);
        $this->assertEquals($conversation->sender_id, $message->sender_id);
    }

    #[Test]
    public function it_creates_message_status_for_recipient(): void
    {
        $conversation = Conversation::factory()->create();

        $message = $this->action->send($conversation->sender, $conversation, ['body' => 'Hi there']);

        $this->assertDatabaseHas('chat_message_status', [
            'message_id' => $message->id,
            'recipient_id' => $conversation->recipient_id,
        ]);
    }

    #[Test]
    public function it_updates_conversation_last_message_at(): void
    {
        $conversation = Conversation::factory()->create();
        $this->assertNull($conversation->last_message_at);

        $message = $this->action->send($conversation->sender, $conversation, ['body' => 'Hey']);

        $conversation->refresh();
        $this->assertNotNull($conversation->last_message_at);
        $this->assertEquals(
            $message->created_at->toDateTimeString(),
            $conversation->last_message_at->toDateTimeString()
        );
    }

    #[Test]
    public function recipient_can_also_send_messages(): void
    {
        $conversation = Conversation::factory()->create();

        $message = $this->action->send($conversation->recipient, $conversation, ['body' => 'Reply']);

        $this->assertEquals($conversation->recipient_id, $message->sender_id);
        $this->assertDatabaseHas('chat_message_status', [
            'message_id' => $message->id,
            'recipient_id' => $conversation->sender_id,
        ]);
    }

    #[Test]
    public function it_throws_when_sender_is_not_a_participant(): void
    {
        $conversation = Conversation::factory()->create();
        $outsider = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        $this->action->send($outsider, $conversation, ['body' => 'Sneaky']);
    }
}
