<?php

namespace App\Listeners;

use App\Events\MentionEvent;
use App\Notifications\MentionNotification;

class MentionListener
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
    public function handle(MentionEvent $event)
    {
        $event->getUser()->notify(new MentionNotification($event->getModel()));
    }
}
