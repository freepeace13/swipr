<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\CreateConversation;
use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateConversationTest extends TestCase
{
    use RefreshDatabase;

    private CreateConversation $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateConversation;
    }

    #[Test]
    public function it_creates_a_conversation_between_two_users(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $conversation = $this->action->execute($sender, $recipient);

        $this->assertInstanceOf(Conversation::class, $conversation);
        $this->assertTrue($conversation->exists);
        $this->assertEquals($sender->id, $conversation->sender_id);
        $this->assertEquals($recipient->id, $conversation->recipient_id);
    }

    #[Test]
    public function it_returns_existing_conversation_instead_of_creating_duplicate(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $first = $this->action->execute($sender, $recipient);
        $second = $this->action->execute($sender, $recipient);

        $this->assertEquals($first->id, $second->id);
        $this->assertDatabaseCount('chat_conversations', 1);
    }

    #[Test]
    public function it_finds_existing_conversation_regardless_of_participant_order(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $first = $this->action->execute($userA, $userB);
        $reversed = $this->action->execute($userB, $userA);

        $this->assertEquals($first->id, $reversed->id);
        $this->assertDatabaseCount('chat_conversations', 1);
    }

    #[Test]
    public function it_throws_when_creating_conversation_with_self(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        $this->action->execute($user, $user);
    }
}
