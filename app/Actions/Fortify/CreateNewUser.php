<?php

namespace App\Actions\Fortify;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    const MIN_AGE = 18;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(self::MIN_AGE)->toDateString()],
            'gender' => ['required', new Enum(Gender::class)],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'birthdate' => $input['birthdate'],
            'gender' => $input['gender'],
        ]);
    }
}
