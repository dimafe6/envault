<?php

namespace App\Listeners\Files;

use App\Events\Files\CreatedEvent;

class LogCreationListener
{
    /**
     * Handle the event.
     *
     * @param CreatedEvent $event
     * @return void
     */
    public function handle(CreatedEvent $event)
    {
        $event->app->log()->create([
            'action'      => 'file.created',
            'description' => "The file {$event->file->name} was added to the {$event->app->name} app.",
        ]);
    }
}
