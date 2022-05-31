<?php

namespace App\Notifications;

use App\Models\Variable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\WebhookServer\WebhookCall;

class VariableCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Variable
     */
    public $variable;

    /**
     * Create a new notification instance.
     *
     * @param Variable $variable
     * @return void
     */
    public function __construct(Variable $variable)
    {
        $this->variable = $variable;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', WebhookChannel::class];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $channel = $notifiable->slack_notification_channel ? "#{$notifiable->slack_notification_channel}" : '#general';

        if(!$notifiable->slack_notification_channel) {
            return null;
        }

        return (new SlackMessage())
            ->success()
            ->from(config('app.name'))
            ->to($channel)
            ->content('An environment variable was added!')
            ->attachment(function ($attachment) use ($notifiable) {
                $attachment->title(
                    $notifiable->name,
                    route('apps.show', [
                        'app' => $notifiable->id,
                    ])
                )
                    ->content('Please run `npx envault` to sync your environment!')
                    ->fields([
                        'Key'     => $this->variable->key,
                        'Version' => "v{$this->variable->latest_version->id}",
                    ]);
            });
    }

    /**
     * @param $notifiable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function toWebhook($notifiable)
    {
        if ($url = $notifiable->webhook_url) {
            WebhookCall::create()
                ->url($url)
                ->payload([
                    'message' => sprintf("An environment variable '%s' was added!", $this->variable->key ?? '')
                ])
                ->meta(['app_id' => $this->variable->app_id])
                ->doNotSign()
                ->dispatch();
        }
    }
}
