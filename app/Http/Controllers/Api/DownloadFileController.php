<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CheckTokenTrait;
use App\Models\App;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadFileController extends Controller
{
    use CheckTokenTrait;

    /**
     * @param App $app
     * @param string $token
     * @param string $uuid
     * @return StreamedResponse
     */
    public function __invoke(App $app, string $token, string $uuid)
    {
        $this->checkToken($app, $token);

        /** @var File $file */
        $file = $app->files()->where(['uuid' => $uuid])->firstOrFail();

        return Storage::disk(config('filesystems.secure_files_disk'))->download($file->path, $file->name);
    }
}
