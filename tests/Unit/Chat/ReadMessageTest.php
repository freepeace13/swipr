<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\ReadMessage;
use App\Actions\Chat\SendMessage;
use App\Models\Chat\Conversation;
use App\Models\Chat\MessageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReadMessageTest extends TestCase
{
    use RefreshDatabase;

    private ReadMessage $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ReadMessage;
    }

    #[Test]
    public function it_marks_a_single_message_as_read(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Hello']);

        $status = $this->action->read($conversation->recipient, $message);

        $this->assertNotNull($status->read_at);
        $this->assertNotNull($status->delivered_at);
    }

    #[Test]
    public function it_preserves_existing_delivered_at(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Hello']);

        $deliveredAt = now()->subMinutes(5);
        MessageStatus::where('message_id', $message->id)->update(['delivered_at' => $deliveredAt]);

        $status = $this->action->read($conversation->recipient, $message);

        $this->assertEquals($deliveredAt->toDateTimeString(), $status->delivered_at->toDateTimeString());
        $this->assertNotNull($status->read_at);
    }

    #[Test]
    public function it_creates_status_if_none_exists(): void
    {
        $conversation = Conversation::factory()->create();
        $message = $conversation->messages()->create([
            'sender_id' => $conversation->sender_id,
            'body' => 'Test',
            'type' => 'text',
        ]);

        $status = $this->action->read($conversation->recipient, $message);

        $this->assertInstanceOf(MessageStatus::class, $status);
        $this->assertTrue($status->exists);
        $this->assertEquals($message->id, $status->message_id);
        $this->assertEquals($conversation->recipient_id, $status->recipient_id);
    }
}
