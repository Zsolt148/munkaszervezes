<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDeletionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, StatefulGuard $guard)
    {
        /** @var Admin $user */
        $user = $request->user('admin');

        $user->deleteProfilePhoto();
        $user->delete();

        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(url('/'));
    }
}
