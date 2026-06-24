<?php

namespace App\Actions\Fortify;

use App\Enums\Gender;
use App\Enums\InterestedIn;
use App\Enums\LookingFor;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'birthdate' => [
                'required',
                'date',
                'before_or_equal:'.now()->subYears(18)->toDateString(), // 18+ only
                'after_or_equal:'.now()->subYears(100)->toDateString(),
            ],
            'gender' => ['required', Rule::enum(Gender::class)],
            'looking_for' => ['required', Rule::enum(LookingFor::class)],
            'interested_in' => ['required', Rule::enum(InterestedIn::class)],
            'min_age_preference' => [
                'required',
                'integer',
                'min:18',
                'max:'.$user->age + 20,  // can't set min higher than 20yrs above own age
                'lte:max_age_preference',
            ],
            'max_age_preference' => [
                'required',
                'integer',
                'min:18',
                'max:99',
                'gte:min_age_preference',
            ],
            'flexible_on_age' => ['required', 'boolean'],
        ])->validateWithBag('updateProfileInformation');

        $attributes = [
            'name' => $input['name'],
            'bio' => $input['bio'] ?? null,
            'birthdate' => $input['birthdate'],
            'gender' => $input['gender'],
            'looking_for' => $input['looking_for'],
            'interested_in' => $input['interested_in'],
            'min_age_preference' => $input['min_age_preference'],
            'max_age_preference' => $input['max_age_preference'],
            'flexible_on_age' => $input['flexible_on_age'],
        ];

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $attributes, $input['email']);
        } else {
            $user->forceFill([
                ...$attributes,
                'email' => $input['email'],
            ])->save();
        }
    }

    protected function updateVerifiedUser(User $user, array $attributes, string $email): void
    {
        $user->forceFill([
            ...$attributes,
            'email' => $email,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
