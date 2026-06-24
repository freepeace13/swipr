<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\DeleteMessage;
use App\Actions\Chat\SendMessage;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteMessageTest extends TestCase
{
    use RefreshDatabase;

    private DeleteMessage $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteMessage;
    }

    #[Test]
    public function it_soft_deletes_own_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Delete me']);

        $this->action->delete($conversation->sender, $message);

        $this->assertSoftDeleted('chat_messages', ['id' => $message->id]);
    }

    #[Test]
    public function it_throws_when_deleting_someone_elses_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Not yours']);

        $this->expectException(InvalidArgumentException::class);

        $this->action->delete($conversation->recipient, $message);
    }

    #[Test]
    public function soft_deleted_message_is_excluded_from_default_queries(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Gone']);

        $this->action->delete($conversation->sender, $message);

        $this->assertNull(Message::find($message->id));
        $this->assertNotNull(Message::withTrashed()->find($message->id));
    }
}
