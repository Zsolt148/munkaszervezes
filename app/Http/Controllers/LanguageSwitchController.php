<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageSwitchController extends Controller
{
    public function __invoke(Request $request, string $language)
    {
        session()->put('locale', $language);

        if (Auth::check()) {
            Auth::user()->update([
                'locale' => $language,
            ]);
        }

        return redirect()->back()->with('success', __('Language changed'));
    }
}
