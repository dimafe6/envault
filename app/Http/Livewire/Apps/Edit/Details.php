<?php

namespace App\Http\Livewire\Apps\Edit;

use App\Models\App;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Details extends Component
{
    use AuthorizesRequests;

    /**
     * @var \App\Models\App
     */
    public $app;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int $token_lifetime
     */
    public $token_lifetime = 10;

    /**
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy()
    {
        $this->authorize('delete', $this->app);

        $this->app->delete();

        $this->emit('app.deleted', $this->app->id);

        event(new \App\Events\Apps\DeletedEvent($this->app));

        redirect()->route('apps.index');
    }

    /**
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update()
    {
        $this->authorize('update', $this->app);

        $this->validate([
            'name' => ['required'],
            'token_lifetime' => ['required', 'integer', 'min:1']
        ]);

        $oldName = $this->app->name;

        $this->app->name = $this->name;
        $this->app->token_lifetime = $this->token_lifetime;

        $this->app->save();

        $this->emit('app.updated', $this->app->id);

        if ($this->app->wasChanged('name')) {
            event(new \App\Events\Apps\NameUpdatedEvent($this->app, $oldName, $this->app->name));
        }

        $this->mount($this->app->refresh());
    }

    /**
     * @param \App\Models\App $app
     * @return void
     */
    public function mount(App $app)
    {
        $this->app = $app;
        $this->name = $app->name;
        $this->token_lifetime = $app->token_lifetime;
    }

    /**
     * @param string $field
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updated($field)
    {
        $this->validateOnly($field, [
            'name' => ['required'],
            'token_lifetime' => ['required', 'integer', 'min:1']
        ]);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('apps.edit.details');
    }
}
