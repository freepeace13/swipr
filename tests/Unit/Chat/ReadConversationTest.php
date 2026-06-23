<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\ReadConversation;
use App\Actions\Chat\SendMessage;
use App\Models\Chat\Conversation;
use App\Models\Chat\MessageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReadConversationTest extends TestCase
{
    use RefreshDatabase;

    private ReadConversation $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ReadConversation;
    }

    #[Test]
    public function it_marks_all_unread_messages_as_read(): void
    {
        $conversation = Conversation::factory()->create();
        $sendMessage = new SendMessage;

        $sendMessage->execute($conversation, $conversation->sender, 'Message 1');
        $sendMessage->execute($conversation, $conversation->sender, 'Message 2');
        $sendMessage->execute($conversation, $conversation->sender, 'Message 3');

        $updated = $this->action->execute($conversation, $conversation->recipient);

        $this->assertEquals(3, $updated);
        $this->assertEquals(
            0,
            MessageStatus::where('recipient_id', $conversation->recipient_id)->whereNull('read_at')->count()
        );
    }

    #[Test]
    public function it_sets_delivered_at_when_marking_as_read(): void
    {
        $conversation = Conversation::factory()->create();
        (new SendMessage)->execute($conversation, $conversation->sender, 'Hello');

        $this->action->execute($conversation, $conversation->recipient);

        $status = MessageStatus::where('recipient_id', $conversation->recipient_id)->first();
        $this->assertNotNull($status->delivered_at);
        $this->assertNotNull($status->read_at);
    }

    #[Test]
    public function it_does_not_affect_already_read_messages(): void
    {
        $conversation = Conversation::factory()->create();
        $sendMessage = new SendMessage;

        $msg1 = $sendMessage->execute($conversation, $conversation->sender, 'Old');
        $status = MessageStatus::where('message_id', $msg1->id)->first();
        $readTime = now()->subHour();
        $status->update(['read_at' => $readTime, 'delivered_at' => $readTime]);

        $sendMessage->execute($conversation, $conversation->sender, 'New');

        $updated = $this->action->execute($conversation, $conversation->recipient);

        $this->assertEquals(1, $updated);
        $status->refresh();
        $this->assertEquals($readTime->toDateTimeString(), $status->read_at->toDateTimeString());
    }

    #[Test]
    public function it_returns_zero_when_no_unread_messages(): void
    {
        $conversation = Conversation::factory()->create();

        $updated = $this->action->execute($conversation, $conversation->recipient);

        $this->assertEquals(0, $updated);
    }
}
