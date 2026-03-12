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
}
