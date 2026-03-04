<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\CityTranslation;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Saudi Arabia Cities
        $saudiCities = [
            [
                'governorate_name' => 'Riyadh',
                'name_ar' => 'الرياض',
                'name_en' => 'Riyadh',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Riyadh',
                'name_ar' => 'الدرعية',
                'name_en' => 'Diriyah',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Makkah',
                'name_ar' => 'مكة',
                'name_en' => 'Mecca',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Makkah',
                'name_ar' => 'جدة',
                'name_en' => 'Jeddah',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Medina',
                'name_ar' => 'المدينة المنورة',
                'name_en' => 'Medina',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Eastern',
                'name_ar' => 'الدمام',
                'name_en' => 'Dammam',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Eastern',
                'name_ar' => 'الخبر',
                'name_en' => 'Khobar',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Qassim',
                'name_ar' => 'بريدة',
                'name_en' => 'Buraidah',
                'is_active' => true
            ]
        ];

        // Egypt Cities
        $egyptCities = [
            [
                'governorate_name' => 'Cairo',
                'name_ar' => 'القاهرة',
                'name_en' => 'Cairo',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Cairo',
                'name_ar' => 'الجيزة',
                'name_en' => 'Giza',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Alexandria',
                'name_ar' => 'الإسكندرية',
                'name_en' => 'Alexandria',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Alexandria',
                'name_ar' => 'برج العرب',
                'name_en' => 'Borg El Arab',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Ismailia',
                'name_ar' => 'الإسماعيلية',
                'name_en' => 'Ismailia',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Hurghada',
                'name_ar' => 'الغردقة',
                'name_en' => 'Hurghada',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Hurghada',
                'name_ar' => 'شرم الشيخ',
                'name_en' => 'Sharm El Sheikh',
                'is_active' => true
            ]
        ];

        // UAE Cities
        $uaeCities = [
            [
                'governorate_name' => 'Abu Dhabi',
                'name_ar' => 'أبو ظبي',
                'name_en' => 'Abu Dhabi',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Dubai',
                'name_ar' => 'دبي',
                'name_en' => 'Dubai',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Dubai',
                'name_ar' => 'الشارقة',
                'name_en' => 'Sharjah',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Sharjah',
                'name_ar' => 'عجمان',
                'name_en' => 'Ajman',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Ras Al Khaimah',
                'name_ar' => 'رأس الخيمة',
                'name_en' => 'Ras Al Khaimah',
                'is_active' => true
            ],
            [
                'governorate_name' => 'Ras Al Khaimah',
                'name_ar' => 'الفجيرة',
                'name_en' => 'Fujairah',
                'is_active' => true
            ]
        ];

        $allCities = array_merge($saudiCities, $egyptCities, $uaeCities);

        foreach ($allCities as $cityData) {
            $governorate = \App\Models\Governorate::whereHas('translations', function($query) use ($cityData) {
                $query->where('name', $cityData['governorate_name']);
            })->first();

            if ($governorate) {
                $city = City::updateOrCreate(
                    [
                        'governorate_id' => $governorate->id,
                        'local' => 'en'
                    ],
                    [
                        'governorate_id' => $governorate->id,
                        'is_active' => $cityData['is_active']
                    ]
                );

                // Create translations
                CityTranslation::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'local' => 'ar'
                    ],
                    [
                        'city_id' => $city->id,
                        'name' => $cityData['name_ar']
                    ]
                );

                CityTranslation::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'local' => 'en'
                    ],
                    [
                        'city_id' => $city->id,
                        'name' => $cityData['name_en']
                    ]
                );
            }
        }
    }
}
