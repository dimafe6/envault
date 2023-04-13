<?php

namespace App\Http\Livewire\Apps;

use App\Models\App;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    /** @var App */
    public $app;

    /**
     * @param App $app
     * @return void
     *
     * @throws AuthorizationException
     */
    public function mount(App $app)
    {
        $this->authorize('view', $app);

        $this->app = $app;
    }

    /**
     * @return View
     */
    public function render()
    {
        return view('apps.show');
    }
}
