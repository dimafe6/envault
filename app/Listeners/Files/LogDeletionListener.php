<?php

namespace App\Listeners\Files;

use App\Events\Files\DeletedEvent;

class LogDeletionListener
{
    /**
     * Handle the event.
     *
     * @param DeletedEvent $event
     * @return void
     */
    public function handle(DeletedEvent $event)
    {
        $event->app->log()->create([
            'action'      => 'file.deleted',
            'description' => "The file {$event->fileName} was removed from the {$event->app->name} app.",
        ]);
    }
}
