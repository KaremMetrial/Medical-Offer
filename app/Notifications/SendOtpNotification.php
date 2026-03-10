<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Contracts\SmsNotificationInterface;
use App\Notifications\Contracts\WhatsAppNotificationInterface;

class SendOtpNotification extends Notification implements ShouldQueue, SmsNotificationInterface, WhatsAppNotificationInterface
{
    use Queueable;

    protected $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->phone) {
            $channels[] = SmsChannel::class;
            $channels[] = WhatsAppChannel::class;
        }

        if ($notifiable->email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('otp.notification_subject'))
            ->line(__('otp.notification_message', ['otp' => $this->otp]))
            ->line(__('otp.notification_expiry_note'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return __('otp.notification_message', ['otp' => $this->otp]);
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): string
    {
        return __('otp.notification_message', ['otp' => $this->otp]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
        ];
    }
}
