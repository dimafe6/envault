<?php

namespace App\Http\Livewire\Files;

use App\Events\Files\CreatedEvent;
use App\Models\App;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Index extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    /**
     * @var App
     */
    public $app;

    /**
     * @var TemporaryUploadedFile
     */
    public $file;

    public $files = [];

    protected $listeners = [
        'file.created' => '$refresh',
        'file.deleted' => '$refresh',
    ];

    /**
     * @param TemporaryUploadedFile $uploadedFile
     * @return void
     */
    public function updatedFile(TemporaryUploadedFile $uploadedFile)
    {
        $this->validate([
            'file' => 'file|max:102400', // 100MB Max
        ]);

        $md5 = md5_file($uploadedFile->path());

        $fileExists = $this->app->files()
            ->where('name', $uploadedFile->getClientOriginalName())
            ->where('md5', $md5)
            ->exists();

        if ($fileExists) {
            $this->addError('file', 'The same file already uploaded!');
        } else {
            $uploadedFile->storeAs($this->app->id, $uploadedFile->getClientOriginalName());

            $file = $this->app->files()->create(['name' => $uploadedFile->getClientOriginalName(), 'md5' => $md5]);

            $this->emit('file.created');

            event(new CreatedEvent($this->app, $file));

            $this->resetErrorBag('file');
        }

        $uploadedFile->delete();

        $this->file = null;
    }

    /**
     * @param App $app
     * @return void
     */
    public function mount(App $app)
    {
        $this->app = $app;
    }

    /**
     * @return View
     */
    public function render()
    {
        $this->files = $this->app->files()->orderBy('created_at')->get();

        return view('files.index');
    }
}
