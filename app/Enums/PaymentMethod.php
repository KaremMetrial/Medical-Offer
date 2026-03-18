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
    public static function optionsWithSelected($selected): array
    {
        $options['items'] = self::options();
        $options['selected'] = $selected;
        $options['selected_label'] = $selected ? self::getLabelByValue($selected) : null;
        $options['label'] = __('message.payment_method');
        $options['key'] = 'payment_method';
        return $options;
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::WALLET->value => __('message.wallet'),
            self::ONLINE->value => __('message.online_payment'),
            default => null,
        };
    }
}
