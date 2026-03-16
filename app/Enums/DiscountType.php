<?php

namespace App\Enums;

enum DiscountType: string
{
    case TEN_AND_TWENTY = 'ten_and_twenty';
    case TWENTY_AND_FORTY = 'twenty_and_forty';
    case FORTY_AND_SIXTY = 'forty_and_sixty';
    case SIXTY_AND_EIGHTY = 'sixty_and_eighty';
    case EIGHTY_AND_ONE_HUNDRED = 'eighty_and_one_hundred';

    public function getLabel(): string
    {
        return match ($this) {
            self::TEN_AND_TWENTY => __('message.ten_and_twenty'),
            self::TWENTY_AND_FORTY => __('message.twenty_and_forty'),
            self::FORTY_AND_SIXTY => __('message.forty_and_sixty'),
            self::SIXTY_AND_EIGHTY => __('message.sixty_and_eighty'),
            self::EIGHTY_AND_ONE_HUNDRED => __('message.eighty_and_one_hundred'),
        };
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::TEN_AND_TWENTY->value => __('message.ten_and_twenty'),
            self::TWENTY_AND_FORTY->value => __('message.twenty_and_forty'),
            self::FORTY_AND_SIXTY->value => __('message.forty_and_sixty'),
            self::SIXTY_AND_EIGHTY->value => __('message.sixty_and_eighty'),
            self::EIGHTY_AND_ONE_HUNDRED->value => __('message.eighty_and_one_hundred'),
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
}
