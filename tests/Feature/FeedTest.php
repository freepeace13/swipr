<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    private function viewer(): User
    {
        return User::factory()->create([
            'gender' => Gender::Woman,
            'interested_in' => InterestedIn::Men,
            'birthdate' => now()->subYears(25)->subDays(5)->toDateString(),
            'min_age_preference' => 20,
            'max_age_preference' => 35,
            'flexible_on_age' => false,
        ]);
    }

    #[Test]
    public function guests_cannot_view_the_feed(): void
    {
        $this->get(route('feeds'))->assertRedirect('/login');
    }

    #[Test]
    public function the_feed_includes_a_compatible_match(): void
    {
        $viewer = $this->viewer();
        User::factory()->create([
            'name' => 'Compatible Match',
            'gender' => Gender::Man,
            'interested_in' => InterestedIn::Women,
            'birthdate' => now()->subYears(28)->subDays(5)->toDateString(),
            'min_age_preference' => 20,
            'max_age_preference' => 40,
            'flexible_on_age' => false,
        ]);

        $this->actingAs($viewer)
            ->get(route('feeds'))
            ->assertOk()
            ->assertSee('Compatible Match');
    }

    #[Test]
    public function the_feed_excludes_an_incompatible_gender(): void
    {
        $viewer = $this->viewer();
        User::factory()->create([
            'name' => 'Incompatible Person',
            'gender' => Gender::Woman,
            'interested_in' => InterestedIn::Men,
            'birthdate' => now()->subYears(28)->subDays(5)->toDateString(),
            'min_age_preference' => 20,
            'max_age_preference' => 40,
            'flexible_on_age' => false,
        ]);

        $this->actingAs($viewer)
            ->get(route('feeds'))
            ->assertOk()
            ->assertDontSee('Incompatible Person');
    }

    #[Test]
    public function the_panel_partial_is_returned_for_feed_page_requests(): void
    {
        $viewer = $this->viewer();
        User::factory()->create([
            'name' => 'Compatible Match',
            'gender' => Gender::Man,
            'interested_in' => InterestedIn::Women,
            'birthdate' => now()->subYears(28)->subDays(5)->toDateString(),
            'min_age_preference' => 20,
            'max_age_preference' => 40,
            'flexible_on_age' => false,
        ]);

        $this->actingAs($viewer)
            ->withHeaders(['X-Feed-Page' => '2'])
            ->get(route('feeds'))
            ->assertOk()
            ->assertSee('Compatible Match');
    }
}
