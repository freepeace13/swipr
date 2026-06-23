<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CurrentUserComposer
{
    public function compose(View $view)
    {
        if ($user = Auth::user()) {
            $view->with('auth', $user);
        }
    }
}
