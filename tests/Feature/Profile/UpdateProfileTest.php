<?php

namespace Tests\Feature\Profile;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Enums\LookingFor;
use App\Models\Interest;
use App\Models\InterestCategory;
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
    public function the_edit_form_is_rendered_on_the_settings_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('Profile Information');
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

    #[Test]
    public function a_user_can_update_their_interests(): void
    {
        $this->seedInterests();
        $user = User::factory()->create();
        $user->interests()->sync(['music_jazz' => ['category_id' => 'music']]);

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload([
                'interests' => ['music_rock', 'sports_running'],
            ]))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'profile-updated');

        $this->assertEqualsCanonicalizing(
            ['music_rock', 'sports_running'],
            $user->fresh()->interestIds()
        );

        // Each interest's category is denormalized onto the pivot.
        $this->assertDatabaseHas('user_interests', [
            'user_id' => $user->id,
            'interest_id' => 'sports_running',
            'category_id' => 'sports',
        ]);

        // The previously selected interest was removed.
        $this->assertDatabaseMissing('user_interests', [
            'user_id' => $user->id,
            'interest_id' => 'music_jazz',
        ]);
    }

    #[Test]
    public function interests_can_be_cleared(): void
    {
        $this->seedInterests();
        $user = User::factory()->create();
        $user->interests()->sync(['music_jazz' => ['category_id' => 'music']]);

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload())
            ->assertSessionHasNoErrors();

        $this->assertSame([], $user->fresh()->interestIds());
    }

    #[Test]
    public function unknown_interests_are_rejected(): void
    {
        $this->seedInterests();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update', ['user' => $user]), $this->validPayload([
                'interests' => ['not_a_real_interest'],
            ]))
            ->assertSessionHasErrors('interests.0', errorBag: 'updateProfileInformation');

        $this->assertSame([], $user->fresh()->interestIds());
    }

    private function seedInterests(): void
    {
        foreach (['music' => 'Music', 'sports' => 'Sports'] as $id => $label) {
            InterestCategory::create(['id' => $id, 'label' => $label]);
        }

        $interests = [
            'music_jazz' => 'music',
            'music_rock' => 'music',
            'sports_running' => 'sports',
        ];

        foreach ($interests as $id => $categoryId) {
            Interest::create([
                'id' => $id,
                'category_id' => $categoryId,
                'label' => ucfirst(str_replace('_', ' ', $id)),
            ]);
        }
    }
}
