<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userAge = $this->user()->age;

        return [
            'birthdate' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(), // 18+ only
                'after_or_equal:' . now()->subYears(100)->toDateString(),
            ],
            'min_age_preference' => [
                'required',
                'integer',
                'min:18',
                'max:' . $userAge + 20,  // can't set min higher than 20yrs above own age
                'lte:max_age_preference',
            ],
            'max_age_preference' => [
                'required',
                'integer',
                'min:18',
                'max:99',
                'gte:min_age_preference',
            ],
        ];
    }
}
