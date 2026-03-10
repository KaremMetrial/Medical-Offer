<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Notifications\Contracts\WhatsAppNotificationInterface;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $notification instanceof WhatsAppNotificationInterface) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('WhatsApp', $notification);

        if (!$to) {
            $to = $notifiable->phone;
        }

        if (!$to || !$message) {
            return;
        }

        // TODO: Implement WhatsApp Logic (e.g. UltraMsg, WhatsApp API, Twilio etc.)
        // Example:
        // App\Services\WhatsAppService::send($to, $message);
    }
}
