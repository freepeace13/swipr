<?php

namespace App\Http\Controllers;

use App\Models\InterestCategory;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __invoke(Request $request)
    {
        $tab = in_array($request->query('tab'), ['profile', 'account'], true)
            ? $request->query('tab')
            : 'profile';

        return view('pages.settings', [
            'tab' => $tab,
            'interestCategories' => InterestCategory::query()
                ->where('is_active', true)
                ->with(['interests' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
