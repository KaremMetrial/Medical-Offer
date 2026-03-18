<?php

namespace App\Enums;

enum RatingType: string
{
    // case ONE = 'one';
    // case TWO = 'two';
    // case THREE = 'three';
    // case FOUR = 'four';
    case FIVE = 'five';

    case FOUR_AND_ABOVE = 'four_and_above';
    case THREE_AND_ABOVE = 'three_and_above';
    case TWO_AND_ABOVE = 'two_and_above';
    // case ONE_AND_ABOVE = 'one_and_above';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIVE => __('message.five'),
            self::FOUR_AND_ABOVE => __('message.four_and_above'),
            self::THREE_AND_ABOVE => __('message.three_and_above'),
            self::TWO_AND_ABOVE => __('message.two_and_above'),
            // self::ONE_AND_ABOVE => __('message.one_and_above'),
        };
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::FIVE->value => __('message.five'),
            self::FOUR_AND_ABOVE->value => __('message.four_and_above'),
            self::THREE_AND_ABOVE->value => __('message.three_and_above'),
            self::TWO_AND_ABOVE->value => __('message.two_and_above'),
            // self::ONE_AND_ABOVE->value => __('message.one_and_above'),
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
        $options['label'] = __('message.rating');
        $options['key'] = 'rating';
        return $options;
    }
}
