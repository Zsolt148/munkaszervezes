<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtherBrowserSessionsController extends Controller
{
    /**
     * Log out from other browser sessions.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, StatefulGuard $guard)
    {
        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return back()->with('success', __('Successfully logged out of other browser sessions'));
    }

    /**
     * Delete the other browser session records from storage.
     *
     * @return void
     */
    protected function deleteOtherSessionRecords(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user('admin')->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
