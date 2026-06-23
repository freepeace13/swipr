<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Enums\LookingFor;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $minAge = fake()->numberBetween(18, 30);

        return [
            'name' => fake()->name(),
            'email' => Str::uuid() . '@' . fake()->safeEmailDomain(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'birthdate' => fake()->dateTimeBetween('-45 years', '-18 years'),
            'gender' => fake()->randomElement(Gender::cases()),
            'looking_for' => fake()->randomElement(LookingFor::cases()),
            'interested_in' => fake()->randomElement(InterestedIn::cases()),
            'min_age_preference' => $minAge,
            'max_age_preference' => fake()->numberBetween($minAge + 5, 55),
            'flexible_on_age' => fake()->boolean(),
            'last_seen_at' => fake()->optional(0.7)->dateTimeBetween('-7 days'),
        ];
    }

    public function withAvatar(): static
    {
        return $this->afterCreating(function (User $user) {
            $name = urlencode($user->name);
            $contents = file_get_contents("https://ui-avatars.com/api/?name={$name}&size=256&background=random&format=png");

            $path = "avatars/{$user->id}.png";
            Storage::disk('public')->put($path, $contents);

            $user->updateQuietly(['avatar' => $path]);
        });
    }

    public function withRandomInterests(int $min = 3, int $max = 8): static
    {
        return $this->afterCreating(function (User $user) use ($min, $max) {
            $picks = Interest::all(['id', 'category_id'])
                ->random(rand($min, $max))
                ->mapWithKeys(fn (Interest $interest) => [
                    $interest->id => [
                        'category_id' => $interest->category_id,
                        'weight' => rand(1, 5),
                    ],
                ]);

            $user->interests()->attach($picks);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
