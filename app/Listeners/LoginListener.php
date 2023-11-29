<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LoginListener
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
    public function handle(Login $event)
    {
        $event->user->updateQuietly([
            'last_login_at' => now(),
        ]);

        activity()
            ->on($event->user)
            ->by($event->user)
            ->event('login')
            ->log('successful admin login');
    }
}
