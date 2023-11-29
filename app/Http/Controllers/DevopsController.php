<?php

namespace App\Http\Controllers;

class DevopsController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function horizon()
    {
        return redirect()->route('horizon.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function telescope()
    {
        abort_if(! config('telescope.enabled'), 404);

        return redirect()->route('telescope');
    }
}
