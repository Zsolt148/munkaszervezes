<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminProxyController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enter(Request $request, $id)
    {
        $user = $request->user('admin');

        Gate::authorize('isSuperadmin', $user);

        $request->session()->put('admin-proxy-original', $user->id);

        Auth::logout();
        Auth::login(Admin::query()->withTrashed()->findOrFail($id));

        return redirect()->route('dashboard');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exit(Request $request)
    {
        Auth::logout();

        if ($id = $request->session()->get('admin-proxy-original')) {

            Auth::login(Admin::query()->withTrashed()->findOrFail($id));

            $request->session()->remove('admin-proxy-original');

            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
}
