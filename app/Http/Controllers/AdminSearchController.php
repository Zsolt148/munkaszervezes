<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $search = $request->get('search');

        if (! $search) {
            return [];
        }

        return Admin::query()
            ->withoutEagerLoads()
            ->with('roles')
            ->active()
            ->forUser($this->user())
            ->where('name', 'like', '%'.$search.'%')
            ->get()
            ->map(function (Admin $admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'profile_photo_url' => $admin->profile_photo_url,
                ];
            })
            ->toArray();
    }
}
