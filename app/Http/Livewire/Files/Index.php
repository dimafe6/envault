<?php

namespace App\Http\Livewire\Files;

use App\Events\Files\CreatedEvent;
use App\Http\Controllers\Api\DownloadFileController;
use App\Models\App;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public $loading = false;

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

        $fileName = $this->uploadedFile->getClientOriginalName();

        if (!Storage::disk('spaces')->putFileAs($this->app->id, $this->uploadedFile, $fileName)) {
            $this->addError('uploadedFile', 'Error when uploading file!');
            $this->reset('uploadedFile');

            return;
        }

        $file = $this->app->files()->create([
            'name' => $fileName,
            'md5'  => $this->md5,
            'size' => $this->uploadedFile->getSize()
        ]);

        $this->uploadedFile->delete();

        $this->resetErrorBag();
        $this->resetValidation();

        $this->emit('file.created');

        event(new CreatedEvent($this->app, $file));

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
     * @return Application|Factory|View
     */
    public function render()
    {
        $this->files = $this->app->files()->orderBy('created_at')->get();

        return view('files.index');
    }

    public function download(App $app, string $token, string $uuid): StreamedResponse
    {
        $this->loading = true;

        $response = (new DownloadFileController)($app, $token, $uuid);

        $this->loading = false;

        return $response;
    }
}
