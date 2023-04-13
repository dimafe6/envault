<?php

namespace App\Events\Files;

use App\Models\App;
use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var App
     */
    public $app;

    /**
     * @var File
     */
    public $file;

    /**
     * Create a new event instance.
     *
     * @param App $app
     * @param File $file
     * @return void
     */
    public function __construct(App $app, File $file)
    {
        $this->app = $app;
        $this->file = $file;
    }
}
