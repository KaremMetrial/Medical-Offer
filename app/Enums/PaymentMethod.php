<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case WALLET = 'wallet';
    case ONLINE = 'online';

    public function getLabel(): string
    {
        return match ($this) {
            self::WALLET => __('message.wallet'),
            self::ONLINE => __('message.online_payment'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case->value, 'label' => $case->getLabel()], self::cases());
    }
}
