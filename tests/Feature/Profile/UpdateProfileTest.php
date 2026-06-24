<?php

namespace Tests\Feature\Profile;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Enums\LookingFor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'bio' => 'A fresh new bio.',
            'birthdate' => now()->subYears(25)->toDateString(),
            'gender' => Gender::Woman->value,
            'looking_for' => LookingFor::LongTermRelationship->value,
            'interested_in' => InterestedIn::Everyone->value,
            'min_age_preference' => 22,
            'max_age_preference' => 40,
            'flexible_on_age' => '1',
        ], $overrides);
    }

    #[Test]
    public function the_edit_screen_can_be_rendered_by_the_owner(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit', ['user' => $user]))
            ->assertOk()
            ->assertSee('Edit profile');
    }

    #[Test]
    public function a_user_cannot_edit_another_users_profile(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit', ['user' => $other]))
            ->assertForbidden();
    }

    #[Test]
    public function a_user_can_update_their_profile(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'profile-updated');

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertSame('A fresh new bio.', $user->bio);
        $this->assertSame(Gender::Woman, $user->gender);
        $this->assertSame(LookingFor::LongTermRelationship, $user->looking_for);
        $this->assertSame(InterestedIn::Everyone, $user->interested_in);
        $this->assertSame(22, $user->min_age_preference);
        $this->assertSame(40, $user->max_age_preference);
        $this->assertTrue($user->flexible_on_age);
    }

    #[Test]
    public function changing_the_email_resets_verification_status(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload([
                'email' => 'new@example.com',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function a_user_cannot_update_another_users_profile(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['name' => 'Original']);

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $other]), $this->validPayload())
            ->assertForbidden();

        $this->assertSame('Original', $other->fresh()->name);
    }

    #[Test]
    public function min_age_preference_must_not_exceed_max(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload([
                'min_age_preference' => 50,
                'max_age_preference' => 30,
            ]))
            ->assertSessionHasErrors('min_age_preference', errorBag: 'updateProfileInformation');
    }

    #[Test]
    public function birthdate_must_be_at_least_18_years_ago(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload([
                'birthdate' => now()->subYears(15)->toDateString(),
            ]))
            ->assertSessionHasErrors('birthdate', errorBag: 'updateProfileInformation');
    }
}
