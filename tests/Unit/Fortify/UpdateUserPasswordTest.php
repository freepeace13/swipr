<?php

namespace Tests\Unit\Fortify;

use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    private UpdateUserPassword $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateUserPassword;
    }

    #[Test]
    public function it_updates_the_password_when_the_current_password_matches(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $this->actingAs($user);

        $this->action->update($user, [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    #[Test]
    public function it_throws_when_the_current_password_is_wrong(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->action->update($user, [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);
    }

    #[Test]
    public function it_throws_when_the_new_password_is_not_confirmed(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->action->update($user, [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'mismatch',
        ]);
    }
}
