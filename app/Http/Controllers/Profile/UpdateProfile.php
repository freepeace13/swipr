<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateProfile extends Controller
{
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        User $user,
        UpdatesUserProfileInformation $updater
    ) {
        $this->authorize('update', $user);

        $updater->update($user, $request->all());

        return back()->with('status', 'profile-updated');
    }
}
