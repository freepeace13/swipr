<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AgePreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgePreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AgePreferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AgePreferenceService;
    }

    private function userAged(int $age): User
    {
        // subtract a few days so the birthday has definitely passed this year
        return User::factory()->make([
            'birthdate' => now()->subYears($age)->subDays(5)->toDateString(),
        ]);
    }

    public static function ageBracketProvider(): array
    {
        return [
            // age, expected min, expected max
            'young adult clamps min to 18' => [19, 18, 24],
            'under 25 bracket' => [22, 20, 27],
            'under 35 bracket' => [30, 26, 36],
            'under 50 bracket' => [40, 34, 48],
            'fifty and over default bracket' => [60, 50, 70],
        ];
    }

    #[Test]
    #[DataProvider('ageBracketProvider')]
    public function it_returns_the_expected_defaults_for_each_age_bracket(int $age, int $min, int $max): void
    {
        $prefs = $this->service->defaults($this->userAged($age));

        $this->assertSame($min, $prefs['min']);
        $this->assertSame($max, $prefs['max']);
    }

    #[Test]
    public function the_minimum_preference_never_drops_below_eighteen(): void
    {
        $prefs = $this->service->defaults($this->userAged(18));

        $this->assertGreaterThanOrEqual(18, $prefs['min']);
    }
}
