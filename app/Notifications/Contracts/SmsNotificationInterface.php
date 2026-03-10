<?php

namespace App\Notifications\Contracts;

interface SmsNotificationInterface
{
    public function toSms(object $notifiable): string;
}
