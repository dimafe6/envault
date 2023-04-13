<?php

namespace App\Http\Livewire\Files;

use App\Events\Files\DeletedEvent;
use App\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
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
        $fileName = $this->file->name;

        $this->file->delete();

        $this->emit('file.deleted');
        event(new DeletedEvent($app, $fileName));
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
     * @return View
     */
    public function render()
    {
        return view('files.delete');
    }
}
