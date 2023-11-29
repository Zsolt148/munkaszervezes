<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileNotificationController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => ['required'],
        ]);

        $this->user()->update([
            'email_notifications' => $validated['email_notifications'],
        ]);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('success', __('Successfully updated'));
    }
}
