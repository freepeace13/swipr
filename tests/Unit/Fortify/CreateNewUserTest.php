<?php

namespace Tests\Unit\Fortify;

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateNewUserTest extends TestCase
{
    use RefreshDatabase;

    private CreateNewUser $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateNewUser;
    }

    private function validInput(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthdate' => now()->subYears(25)->toDateString(),
            'gender' => Gender::Woman->value,
        ], $overrides);
    }

    #[Test]
    public function it_creates_a_user_with_valid_input(): void
    {
        $user = $this->action->create($this->validInput());

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('Jane Doe', $user->name);
        $this->assertSame('jane@example.com', $user->email);
        $this->assertSame(Gender::Woman, $user->gender);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    #[Test]
    public function it_hashes_the_password(): void
    {
        $user = $this->action->create($this->validInput());

        $this->assertNotSame('password', $user->password);
        $this->assertTrue(password_verify('password', $user->password));
    }

    #[Test]
    public function it_requires_the_password_to_be_confirmed(): void
    {
        $this->expectException(ValidationException::class);

        $this->action->create($this->validInput([
            'password_confirmation' => 'different',
        ]));
    }

    #[Test]
    public function it_rejects_users_under_eighteen(): void
    {
        $this->expectException(ValidationException::class);

        $this->action->create($this->validInput([
            'birthdate' => now()->subYears(16)->toDateString(),
        ]));
    }

    #[Test]
    public function it_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->expectException(ValidationException::class);

        $this->action->create($this->validInput());
    }

    #[Test]
    public function it_rejects_an_invalid_gender(): void
    {
        $this->expectException(ValidationException::class);

        $this->action->create($this->validInput([
            'gender' => 'not-a-gender',
        ]));
    }
}
