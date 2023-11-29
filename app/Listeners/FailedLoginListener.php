<?php

namespace App\Listeners;

use App\Events\Failed;

class FailedLoginListener
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
    public function handle(Failed $event)
    {
        if ($user = $event->user) {
            activity()
                ->on($user)
                ->withProperties([
                    'ip' => $event->credentials['ip'] ?? '-',
                    'password' => $event->credentials['password'],
                    'login_attempts' => $user->login_attempts,
                ])
                ->byAnonymous()
                ->event('failed-login')
                ->log('failed login attempt');
        }

    }
}
