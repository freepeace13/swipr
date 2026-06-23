<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load('interests.category');

        return view('pages.profile.show', [
            'user' => $user
        ]);
    }

    public function edit()
    {
        return view('pages.profile.edit');
    }

    public function update(UpdateProfileRequest $request)
    {
        //
    }
}
