<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\GovernorateTranslation;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        // Saudi Arabia Governorates
        $saudiGovernorates = [
            [
                'country_code' => '+966',
                'name_ar' => 'الرياض',
                'name_en' => 'Riyadh',
                'is_active' => true
            ],
            [
                'country_code' => '+966',
                'name_ar' => 'مكة',
                'name_en' => 'Makkah',
                'is_active' => true
            ],
            [
                'country_code' => '+966',
                'name_ar' => 'المدينة',
                'name_en' => 'Medina',
                'is_active' => true
            ],
            [
                'country_code' => '+966',
                'name_ar' => 'الشرقية',
                'name_en' => 'Eastern',
                'is_active' => true
            ],
            [
                'country_code' => '+966',
                'name_ar' => 'القصيم',
                'name_en' => 'Qassim',
                'is_active' => true
            ]
        ];

        // Egypt Governorates
        $egyptGovernorates = [
            [
                'country_code' => '+20',
                'name_ar' => 'القاهرة',
                'name_en' => 'Cairo',
                'is_active' => true
            ],
            [
                'country_code' => '+20',
                'name_ar' => 'الإسكندرية',
                'name_en' => 'Alexandria',
                'is_active' => true
            ],
            [
                'country_code' => '+20',
                'name_ar' => 'الجيزة',
                'name_en' => 'Giza',
                'is_active' => true
            ],
            [
                'country_code' => '+20',
                'name_ar' => 'الإسماعيلية',
                'name_en' => 'Ismailia',
                'is_active' => true
            ],
            [
                'country_code' => '+20',
                'name_ar' => 'الغردقة',
                'name_en' => 'Hurghada',
                'is_active' => true
            ]
        ];

        // UAE Governorates
        $uaeGovernorates = [
            [
                'country_code' => '+971',
                'name_ar' => 'أبو ظبي',
                'name_en' => 'Abu Dhabi',
                'is_active' => true
            ],
            [
                'country_code' => '+971',
                'name_ar' => 'دبي',
                'name_en' => 'Dubai',
                'is_active' => true
            ],
            [
                'country_code' => '+971',
                'name_ar' => 'الشارقة',
                'name_en' => 'Sharjah',
                'is_active' => true
            ],
            [
                'country_code' => '+971',
                'name_ar' => 'عجمان',
                'name_en' => 'Ajman',
                'is_active' => true
            ],
            [
                'country_code' => '+971',
                'name_ar' => 'رأس الخيمة',
                'name_en' => 'Ras Al Khaimah',
                'is_active' => true
            ]
        ];

        $allGovernorates = array_merge($saudiGovernorates, $egyptGovernorates, $uaeGovernorates);

        foreach ($allGovernorates as $governorateData) {
            $country = \App\Models\Country::where('phone_code', $governorateData['country_code'])->first();

            if ($country) {
                $governorate = Governorate::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'local' => 'en'
                    ],
                    [
                        'country_id' => $country->id,
                        'is_active' => $governorateData['is_active']
                    ]
                );

                // Create translations
                GovernorateTranslation::updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'local' => 'ar'
                    ],
                    [
                        'governorate_id' => $governorate->id,
                        'name' => $governorateData['name_ar']
                    ]
                );

                GovernorateTranslation::updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'local' => 'en'
                    ],
                    [
                        'governorate_id' => $governorate->id,
                        'name' => $governorateData['name_en']
                    ]
                );
            }
        }
    }
}
