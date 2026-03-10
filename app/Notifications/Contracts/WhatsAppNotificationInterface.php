<?php

namespace App\Notifications\Contracts;

interface WhatsAppNotificationInterface
{
    public function toWhatsApp(object $notifiable): string;
}
