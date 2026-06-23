<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('settings', [
            'user' => $user
        ]);
    }
}
