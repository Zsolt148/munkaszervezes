<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmPassword;

class ConfirmablePasswordController extends Controller
{
    /**
     * @return void
     */
    public function store(Request $request, StatefulGuard $guard)
    {
        $validated = $request->validate([
            'password' => ['required'],
        ]);

        $user = $request->user();

        $confirmed = app(ConfirmPassword::class)(
            $guard, $user, $validated['password']
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return response()->json([
            'password' => $request->password,
        ]);
    }
}
