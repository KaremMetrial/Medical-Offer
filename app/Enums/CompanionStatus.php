<?php

namespace App\Enums;

enum CompanionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('message.pending'),
            self::APPROVED => __('message.approved'),
            self::REJECTED => __('message.rejected'),
        };
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::PENDING->value => __('message.pending'),
            self::APPROVED->value => __('message.approved'),
            self::REJECTED->value => __('message.rejected'),
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
        $options['label'] = __('message.companion_status');
        $options['key'] = 'companion_status';
        return $options;
    }

}
