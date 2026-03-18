<?php

namespace App\Enums;

enum GenderType: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => __('message.male'),
            self::FEMALE => __('message.female'),
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
        $options['label'] = __('message.gender');
        $options['key'] = 'gender';
        return $options;
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::MALE->value => __('message.male'),
            self::FEMALE->value => __('message.female'),
            default => null,
        };
    }
}
