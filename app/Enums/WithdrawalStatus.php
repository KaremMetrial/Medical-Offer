<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('message.pending'),
            self::APPROVED => __('message.approved'),
            self::REJECTED => __('message.rejected'),
            self::COMPLETED => __('message.completed'),
            self::CANCELLED => __('message.cancelled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'primary',
            self::REJECTED => 'danger',
            self::COMPLETED => 'success',
            self::CANCELLED => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::REJECTED => 'heroicon-o-x-circle',
            self::COMPLETED => 'heroicon-o-check-badge',
            self::CANCELLED => 'heroicon-o-no-symbol',
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
