<?php

namespace App\Http\Livewire\Apps;

use App\Events\Apps\CreatedEvent;
use App\Models\App;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    /**
     * @var string
     */
    public $name = '';

    public $token_lifetime = 10;

    /**
     * @return void
     *
     * @throws AuthorizationException
     */
    public function store()
    {
        $this->authorize('create', App::class);

        $app = App::create(
            $this->validate([
                'name'           => ['required'],
                'token_lifetime' => ['required', 'integer', 'min:1']
            ])
        );

        $this->emit('app.created', $app->id);

        event(new CreatedEvent($app));

        redirect()->route('apps.show', [
            'app' => $app->id,
        ]);
    }

    /**
     * @return View
     */
    public function render()
    {
        return view('apps.create');
    }
}
