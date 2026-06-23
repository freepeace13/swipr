<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\DeleteConversation;
use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteConversationTest extends TestCase
{
    use RefreshDatabase;

    private DeleteConversation $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteConversation;
    }

    #[Test]
    public function it_soft_deletes_for_sender(): void
    {
        $conversation = Conversation::factory()->create();

        $this->action->execute($conversation, $conversation->sender);

        $conversation->refresh();
        $this->assertNotNull($conversation->sender_deleted_at);
        $this->assertNull($conversation->recipient_deleted_at);
    }

    #[Test]
    public function it_soft_deletes_for_recipient(): void
    {
        $conversation = Conversation::factory()->create();

        $this->action->execute($conversation, $conversation->recipient);

        $conversation->refresh();
        $this->assertNull($conversation->sender_deleted_at);
        $this->assertNotNull($conversation->recipient_deleted_at);
    }

    #[Test]
    public function both_participants_can_delete_independently(): void
    {
        $conversation = Conversation::factory()->create();

        $this->action->execute($conversation, $conversation->sender);
        $this->action->execute($conversation, $conversation->recipient);

        $conversation->refresh();
        $this->assertNotNull($conversation->sender_deleted_at);
        $this->assertNotNull($conversation->recipient_deleted_at);
    }

    #[Test]
    public function it_throws_when_user_is_not_a_participant(): void
    {
        $conversation = Conversation::factory()->create();
        $outsider = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        $this->action->execute($conversation, $outsider);
    }
}
