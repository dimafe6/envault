<?php

namespace App\Http\Livewire\Apps\Edit;

use App\Events\Apps\WebhookSettingsUpdatedEvent;
use App\Events\Apps\WebhookSetUpEvent;
use App\Models\App;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Webhook extends Component
{
    use AuthorizesRequests;

    /**
     * @var App
     */
    public $app;

    /**
     * @var string
     */
    public $webhook_url = '';

    /**
     * @return void
     *
     * @throws AuthorizationException
     */
    public function update()
    {
        $this->authorize('update', $this->app);

        $this->validate([
            'webhook_url' => ['url', 'active_url', 'starts_with:https://,HTTPS://'],
        ]);

        $this->app->webhook_url = $this->webhook_url;
        $this->app->save();

        if ($this->app->wasChanged(['webhook_url'])) {
            if ($this->app->notificationsEnabled()) {
                $this->emit('app.webhook.set-up', $this->app->id);

                event(new WebhookSetUpEvent($this->app));
            } else {
                $this->emit('app.webhook.updated', $this->app->id);

                event(new WebhookSettingsUpdatedEvent($this->app));
            }
        } else {
            // Configuration changes have not been made
            $this->emit('app.webhook.updated', $this->app->id);
        }

        $this->mount($this->app->refresh());
    }

    /**
     * @param App $app
     * @return void
     */
    public function mount(App $app)
    {
        $this->app = $app;
        $this->webhook_url = $app->webhook_url;
    }

    /**
     * @return View
     */
    public function render()
    {
        return view('apps.edit.webhook');
    }
}
