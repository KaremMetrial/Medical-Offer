<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FirebaseChannel;

class GeneralNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $title, protected string $message)
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FirebaseChannel::class];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->message,
            'data' => [
                'type' => 'general_notification',
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->message,
        ];
    }
}
