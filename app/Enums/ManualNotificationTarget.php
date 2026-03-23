<?php

namespace App\Enums;

enum ManualNotificationTarget: string
{
    case ALL = 'all';
    case SPECIFIC = 'specific';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => __('filament.options.target_type.all'),
            self::SPECIFIC => __('filament.options.target_type.specific'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ALL => 'primary',
            self::SPECIFIC => 'success',
        };
    }
}
