<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case PROVIDER = 'provider';
    case SUPER_ADMIN = 'super_admin';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => __('message.admin'),
            self::USER => __('message.user'),
            self::PROVIDER => __('message.provider'),
            self::SUPER_ADMIN => __('message.super_admin'),
        };
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::ADMIN->value => __('message.admin'),
            self::USER->value => __('message.user'),
            self::PROVIDER->value => __('message.provider'),
            self::SUPER_ADMIN->value => __('message.super_admin'),
            default => null,
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
        $options['label'] = __('message.role');
        $options['key'] = 'role';
        return $options;
    }

}
