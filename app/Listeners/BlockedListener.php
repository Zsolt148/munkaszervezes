<?php

namespace App\Listeners;

use App\Events\Blocked;
use App\Models\Admin;
use App\Models\Role;
use App\Notifications\AdminBlockedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class BlockedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Blocked $event)
    {
        $user = $event->user;

        activity()
            ->on($user)
            ->withProperties([
                'blocked_at' => $user->blocked_at,
                'status' => $user->status,
            ])
            ->byAnonymous()
            ->event('blocked')
            ->log('blocked');

        $admins = Admin::query()
            ->role(Role::admins())
            ->whereKeyNot($user->id)
            ->get();

        Notification::send(
            $admins,
            new AdminBlockedNotification($user)
        );
    }
}
