<?php

namespace App\Services;

use App\Models\User;

class AgePreferenceService
{
    // Sample usage:
    // $prefs = (new AgePreferenceService)->defaults($user);
    // $user->update([
    //     'min_age_preference' => $prefs['min'],
    //     'max_age_preference' => $prefs['max'],
    // ]);
    public function defaults(User $user): array
    {
        $age = $user->age;

        return match(true) {
            $age < 25 => [
                'min' => max(18, $age - 2),
                'max' => $age + 5,
            ],
            $age < 35 => [
                'min' => $age - 4,
                'max' => $age + 6,
            ],
            $age < 50 => [
                'min' => $age - 6,
                'max' => $age + 8,
            ],
            default => [
                'min' => $age - 10,
                'max' => $age + 10,
            ],
        };
    }
}
