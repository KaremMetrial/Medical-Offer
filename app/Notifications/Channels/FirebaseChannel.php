<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FirebaseService;

class FirebaseChannel
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
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
        // Get the FCM tokens from the notifiable
        $tokens = $notifiable->routeNotificationFor('firebase', $notification);

        if (empty($tokens)) {
            return;
        }

        // Get the notification data formatted for Firebase
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        $fcmMessage = $notification->toFirebase($notifiable);

        if (is_array($fcmMessage)) {
            $this->firebaseService->sendToDevice(
                $tokens,
                $fcmMessage['title'] ?? '',
                $fcmMessage['body'] ?? '',
                $fcmMessage['data'] ?? []
            );
        }
    }
}
