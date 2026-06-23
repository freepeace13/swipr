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
        $message = (new SendMessage)->execute($conversation, $conversation->sender, 'Delete me');

        $this->action->execute($message, $conversation->sender);

        $this->assertSoftDeleted('chat_messages', ['id' => $message->id]);
    }

    #[Test]
    public function it_throws_when_deleting_someone_elses_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->execute($conversation, $conversation->sender, 'Not yours');

        $this->expectException(InvalidArgumentException::class);

        $this->action->execute($message, $conversation->recipient);
    }

    #[Test]
    public function soft_deleted_message_is_excluded_from_default_queries(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->execute($conversation, $conversation->sender, 'Gone');

        $this->action->execute($message, $conversation->sender);

        $this->assertNull(Message::find($message->id));
        $this->assertNotNull(Message::withTrashed()->find($message->id));
    }
}
