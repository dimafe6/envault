<?php

namespace App\Http\Livewire\Files;

use App\Events\Files\DeletedEvent;
use App\Models\File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Delete extends Component
{
    use AuthorizesRequests;

    /**
     * @var File
     */
    public $file;

    /**
     * @return void
     */
    public function destroy()
    {
        $app = $this->file->app;

        Storage::disk('spaces')->delete($this->file->path, $this->file->name);

        $this->file->delete();

        $this->emit('file.deleted');
        event(new DeletedEvent($app, $this->file->name));
    }

    /**
     * @param File $file
     * @return void
     */
    public function mount(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        return view('files.delete');
    }
}
