<?php

namespace App\Events\Files;

use App\Models\App;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var App
     */
    public $app;

    /**
     * @var string
     */
    public $fileName;

    /**
     * Create a new event instance.
     *
     * @param App $app
     * @param string $fileName
     * @return void
     */
    public function __construct(App $app, string $fileName)
    {
        $this->app = $app;
        $this->fileName = $fileName;
    }
}
