<?php

namespace App\Enums;

enum CardRequestStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PREPARED = 'prepared';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return __("message.card_status.{$this->value}");
    }

    public static function getLabelByValue($value): ?string
    {
        $case = self::tryFrom($value);
        return $case?->getLabel();
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
        $options['label'] = __('filament.fields.status');
        $options['key'] = 'status';
        return $options;
    }
}
