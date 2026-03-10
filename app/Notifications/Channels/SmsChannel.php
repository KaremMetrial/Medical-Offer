<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Notifications\Contracts\SmsNotificationInterface;
use App\Services\SmsService;

class SmsChannel
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $notification instanceof SmsNotificationInterface) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (!$to) {
            $to = $notifiable->phone;
        }

        if (!$to || !$message) {
            return;
        }

        // Delegate execution to the real SmsService
        $this->smsService->send($to, $message);
    }
}
