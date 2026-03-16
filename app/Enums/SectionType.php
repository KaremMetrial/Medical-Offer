<?php

namespace App\Enums;

enum SectionType: string
{
    case DOCTORS = 'doctors';
    case CENTERS = 'centers';
    case LABS = 'labs';
    case PHARMACIES = 'pharmacies';

    public function getLabel(): string
    {
        return match ($this) {
            self::DOCTORS => __('filament.options.section_type.doctors'),
            self::CENTERS => __('filament.options.section_type.centers'),
            self::LABS => __('filament.options.section_type.labs'),
            self::PHARMACIES => __('filament.options.section_type.pharmacies'),
        };
    }
    public static function getLabelByValue($value): ?string
    {
        return match ($value) {
            self::DOCTORS->value => __('filament.options.section_type.doctors'),
            self::CENTERS->value => __('filament.options.section_type.centers'),
            self::LABS->value => __('filament.options.section_type.labs'),
            self::PHARMACIES->value => __('filament.options.section_type.pharmacies'),
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
