<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function __invoke(User $user)
    {
        $user->load('interests.category');

        return view('profile', compact('user'));
    }
}
