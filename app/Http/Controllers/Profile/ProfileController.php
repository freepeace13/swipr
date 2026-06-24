<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('pages.profile.show', [
            'user' => $user->load('interests.category')
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
