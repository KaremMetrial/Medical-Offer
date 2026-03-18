<?php

namespace App\Enums;

enum RelationshipType: string
{
    case FATHER = 'father';
    case MOTHER = 'mother';
    case SON = 'son';
    case DAUGHTER = 'daughter';
    case SPOUSE = 'spouse';
    case BROTHER = 'brother';
    case SISTER = 'sister';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::FATHER => __('message.father'),
            self::MOTHER => __('message.mother'),
            self::SON => __('message.son'),
            self::DAUGHTER => __('message.daughter'),
            self::SPOUSE => __('message.spouse'),
            self::BROTHER => __('message.brother'),
            self::SISTER => __('message.sister'),
            self::OTHER => __('message.other'),
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
        $options['label'] = __('message.relationship');
        $options['key'] = 'relationship';
        return $options;
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::FATHER->value => __('message.father'),
            self::MOTHER->value => __('message.mother'),
            self::SON->value => __('message.son'),
            self::DAUGHTER->value => __('message.daughter'),
            self::SPOUSE->value => __('message.spouse'),
            self::BROTHER->value => __('message.brother'),
            self::SISTER->value => __('message.sister'),
            self::OTHER->value => __('message.other'),
            default => null,
        };
    }
}
