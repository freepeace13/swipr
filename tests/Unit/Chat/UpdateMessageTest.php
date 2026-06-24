<?php

namespace Tests\Unit\Chat;

use App\Actions\Chat\SendMessage;
use App\Actions\Chat\UpdateMessage;
use App\Models\Chat\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateMessageTest extends TestCase
{
    use RefreshDatabase;

    private UpdateMessage $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateMessage;
    }

    #[Test]
    public function the_sender_can_edit_their_own_message(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Original']);

        $updated = $this->action->update($conversation->sender, $message, ['body' => 'Edited']);

        $this->assertSame('Edited', $updated->body);
        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'body' => 'Edited',
        ]);
    }

    #[Test]
    public function it_throws_when_a_non_sender_tries_to_edit(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Original']);

        $this->expectException(InvalidArgumentException::class);

        $this->action->update($conversation->recipient, $message, ['body' => 'Hijacked']);
    }

    #[Test]
    public function it_does_not_change_the_body_when_a_non_sender_attempts_to_edit(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Original']);

        try {
            $this->action->update($conversation->recipient, $message, ['body' => 'Hijacked']);
        } catch (InvalidArgumentException) {
            // expected
        }

        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'body' => 'Original',
        ]);
    }

    #[Test]
    public function it_validates_that_a_body_is_required(): void
    {
        $conversation = Conversation::factory()->create();
        $message = (new SendMessage)->send($conversation->sender, $conversation, ['body' => 'Original']);

        $this->expectException(ValidationException::class);

        $this->action->update($conversation->sender, $message, ['body' => '']);
    }
}
