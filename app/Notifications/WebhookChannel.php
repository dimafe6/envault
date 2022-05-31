<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WebhookChannel
{
    /**
     * Call the given webhook.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $notification->toWebhook($notifiable);
    }
}