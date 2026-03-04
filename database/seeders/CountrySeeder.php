<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\CountryTranslation;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'phone_code' => '+966',
                'currency_symbol' => '﷼',
                'currency_name' => 'Saudi Riyal',
                'currency_unit' => 'SAR',
                'currency_factor' => 1.0000,
                'flag' => 'flags/sa.png',
                'timezone' => 'Asia/Riyadh',
                'is_active' => true,
                'translations' => [
                    'ar' => ['name' => 'المملكة العربية السعودية'],
                    'en' => ['name' => 'Saudi Arabia']
                ]
            ],
            [
                'phone_code' => '+20',
                'currency_symbol' => 'ج.م',
                'currency_name' => 'Egyptian Pound',
                'currency_unit' => 'EGP',
                'currency_factor' => 1.0000,
                'flag' => 'flags/eg.png',
                'timezone' => 'Africa/Cairo',
                'is_active' => true,
                'translations' => [
                    'ar' => ['name' => 'مصر'],
                    'en' => ['name' => 'Egypt']
                ]
            ],
            [
                'phone_code' => '+971',
                'currency_symbol' => 'د.إ',
                'currency_name' => 'UAE Dirham',
                'currency_unit' => 'AED',
                'currency_factor' => 1.0000,
                'flag' => 'flags/ae.png',
                'timezone' => 'Asia/Dubai',
                'is_active' => true,
                'translations' => [
                    'ar' => ['name' => 'الإمارات العربية المتحدة'],
                    'en' => ['name' => 'United Arab Emirates']
                ]
            ]
        ];

        foreach ($countries as $countryData) {
            $translations = $countryData['translations'];
            unset($countryData['translations']);

            $country = Country::updateOrCreate(
                ['phone_code' => $countryData['phone_code']],
                $countryData
            );

            foreach ($translations as $locale => $translation) {
                CountryTranslation::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'local' => $locale
                    ],
                    $translation
                );
            }
        }
    }
}
