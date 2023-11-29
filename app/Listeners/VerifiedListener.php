<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class VerifiedListener
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
    public function handle(Verified $event)
    {
        activity()
            ->on($event->user)
            ->by($event->user)
            ->event('verified')
            ->log('successful admin verification');
    }
}
