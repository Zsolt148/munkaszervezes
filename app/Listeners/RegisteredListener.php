<?php

namespace App\Listeners;

class RegisteredListener
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
    public function handle($event)
    {
        activity()
            ->on($event->user)
            ->by($event->user)
            ->event('registered')
            ->log('admin registered');
    }
}
