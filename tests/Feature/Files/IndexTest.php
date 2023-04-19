<?php

namespace Tests\Feature\Files;

use App\Models\App;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = User::factory()->state([
            'role' => 'owner',
        ])->create();

        Livewire::actingAs($this->authenticatedUser);
    }

    /** @test */
    public function can_upload_and_delete_file()
    {
        $app = App::factory()->create();

        Storage::fake('local');
        Storage::fake('spaces');

        $file = UploadedFile::fake()->image('avatar.png');

        Livewire::test('apps.show.setup-command', ['app' => $app])
            ->assertNotSet('token', null)
            ->assertEmitted('app.setup-command.generated', $app->id);

        Livewire::test('files.index', ['app' => $app])
            ->set('uploadedFile', $file)
            ->call('save')
            ->assertEmitted('file.created')
            ->assertSet('uploadedFile', null);

        $this->assertDatabaseHas('files', [
            'app_id' => $app->id,
            'name'   => 'avatar.png',
        ]);

        $dbFile = $app->files()->first();

        Storage::disk('spaces')->assertExists($dbFile->path);

        Livewire::test('files.index', ['file' => $dbFile])
            ->call('download', $app, $app->existsToken()->token, $dbFile->uuid)
            ->assertHasNoErrors();

        $this->assertNotNull($dbFile);

        Livewire::test('files.delete', ['file' => $dbFile])
            ->call('destroy')
            ->assertEmitted('file.deleted');

        Storage::disk('spaces')->assertMissing($dbFile->path);
    }
}
