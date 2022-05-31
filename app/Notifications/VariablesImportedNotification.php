<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class VariablesImportedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    public $count;

    /**
     * Create a new notification instance.
     *
     * @param int $count
     * @return void
     */
    public function __construct($count)
    {
        $this->count = $count;
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

        $variableForm = Str::plural('variable', $this->count);
        $wasForm = $this->count > 1 ? 'were' : 'was';

        return (new SlackMessage())
            ->success()
            ->from(config('app.name'))
            ->to($channel)
            ->content("{$this->count} environment {$variableForm} {$wasForm} added!")
            ->attachment(function ($attachment) use ($notifiable) {
                $attachment->title(
                    $notifiable->name,
                    route('apps.show', [
                        'app' => $notifiable->id,
                    ])
                )
                    ->content('Please run `npx envault` to sync your environment!');
            });
    }

    /**
     * @param $notifiable
     * @author Dmytro Feshchenko <dimafe2000@gmail.com>
     */
    public function toWebhook($notifiable)
    {
        $variableForm = Str::plural('variable', $this->count);
        $wasForm = $this->count > 1 ? 'were' : 'was';

        if ($url = $notifiable->webhook_url) {
            WebhookCall::create()
                ->url($url)
                ->payload([
                    'message' => "{$this->count} environment {$variableForm} {$wasForm} added!"
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
