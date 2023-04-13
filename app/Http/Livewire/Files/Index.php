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
    public $uploadedFile;

    public $files = [];

    public $md5 = null;

    protected $listeners = [
        'file.created' => '$refresh',
        'file.deleted' => '$refresh',
    ];

    public function updatedUploadedFile(TemporaryUploadedFile $uploadedFile)
    {
        $this->validate([
            'uploadedFile' => 'file|max:102400', // 100MB Max
        ]);

        $this->md5 = md5_file($this->uploadedFile->path());

        $fileExists = $this->app->files()
            ->where('name', $this->uploadedFile->getClientOriginalName())
            ->where('md5', $this->md5)
            ->exists();

        if ($fileExists) {
            $this->addError('uploadedFile', 'The same file already uploaded!');
            $this->reset('uploadedFile');
        } else {
            $this->resetErrorBag();
            $this->resetValidation();
        }
    }

    /**
     * @return void
     */
    public function save()
    {
        $this->validate([
            'uploadedFile' => 'file|max:102400', // 100MB Max
        ]);

        $this->uploadedFile->storeAs($this->app->id, $this->uploadedFile->getClientOriginalName());

        $file = $this->app->files()->create([
            'name' => $this->uploadedFile->getClientOriginalName(),
            'md5'  => $this->md5,
            'size' => $this->uploadedFile->getSize()
        ]);

        $this->resetErrorBag();
        $this->resetValidation();

        $this->emit('file.created');

        event(new CreatedEvent($this->app, $file));

        $this->uploadedFile->delete();

        $this->reset('uploadedFile');
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
