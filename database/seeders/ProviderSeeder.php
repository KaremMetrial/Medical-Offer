<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Provider;
use App\Models\ProviderTranslation;
use App\Models\ProviderBranch;
use App\Models\City;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'logo' => 'providers/health-care-center.png',
                'cover' => 'providers/health-care-cover.jpg',
                'phone' => '+966112345678',
                'experince_years' => 15,
                'country_code' => '+966',
                'status' => 'active',
                'is_varified' => true,
                'views' => 1250,
                'translations' => [
                    'ar' => [
                        'name' => 'مركز الرعاية الصحية',
                        'title' => 'أفضل خدمات الرعاية الصحية',
                        'description' => 'مركز طبي متكامل يقدم خدمات الرعاية الصحية المتميزة بأحدث التقنيات وأمهر الكوادر الطبية'
                    ],
                    'en' => [
                        'name' => 'Health Care Center',
                        'title' => 'Best Healthcare Services',
                        'description' => 'Comprehensive medical center offering premium healthcare services with latest technology and expert medical staff'
                    ]
                ],
                'branches' => [
                    [
                        'name_ar' => 'فرع الرياض',
                        'name_en' => 'Riyadh Branch',
                        'address' => 'شارع الملك فهد، الرياض',
                        'lat' => 24.7136,
                        'lng' => 46.6753,
                        'phone' => '+966112345678',
                        'working_hours_json' => [
                            'sunday' => ['08:00', '20:00'],
                            'monday' => ['08:00', '20:00'],
                            'tuesday' => ['08:00', '20:00'],
                            'wednesday' => ['08:00', '20:00'],
                            'thursday' => ['08:00', '20:00'],
                            'friday' => ['09:00', '15:00'],
                            'saturday' => ['10:00', '14:00']
                        ],
                        'is_main' => true,
                        'is_active' => true
                    ]
                ]
            ],
            [
                'logo' => 'providers/beauty-center.png',
                'cover' => 'providers/beauty-cover.jpg',
                'phone' => '+20223456789',
                'experince_years' => 10,
                'country_code' => '+20',
                'status' => 'active',
                'is_varified' => true,
                'views' => 890,
                'translations' => [
                    'ar' => [
                        'name' => 'مركز الجمال والعناية',
                        'title' => 'جمالك هو أولويتنا',
                        'description' => 'مركز تجميلي متكامل يقدم أحدث علاجات البشرة والشعر بأيدي خبراء متخصصين'
                    ],
                    'en' => [
                        'name' => 'Beauty & Care Center',
                        'title' => 'Your Beauty is Our Priority',
                        'description' => 'Complete beauty center offering latest skin and hair treatments by expert specialists'
                    ]
                ],
                'branches' => [
                    [
                        'name_ar' => 'فرع القاهرة',
                        'name_en' => 'Cairo Branch',
                        'address' => 'شارع عبد الرحمن خليل، القاهرة',
                        'lat' => 30.0444,
                        'lng' => 31.2357,
                        'phone' => '+20223456789',
                        'working_hours_json' => [
                            'sunday' => ['09:00', '21:00'],
                            'monday' => ['09:00', '21:00'],
                            'tuesday' => ['09:00', '21:00'],
                            'wednesday' => ['09:00', '21:00'],
                            'thursday' => ['09:00', '21:00'],
                            'friday' => ['10:00', '18:00'],
                            'saturday' => ['10:00', '16:00']
                        ],
                        'is_main' => true,
                        'is_active' => true
                    ]
                ]
            ],
            [
                'logo' => 'providers/dental-clinic.png',
                'cover' => 'providers/dental-cover.jpg',
                'phone' => '+97141234567',
                'experince_years' => 12,
                'country_code' => '+971',
                'status' => 'active',
                'is_varified' => true,
                'views' => 1560,
                'translations' => [
                    'ar' => [
                        'name' => 'عيادة الأسنان المتقدمة',
                        'title' => 'ابتسامتك الجميلة تهمنا',
                        'description' => 'عيادة أسنان متطورة تقدم جميع خدمات طب الأسنان التجميلي والعلاجي بأحدث التقنيات'
                    ],
                    'en' => [
                        'name' => 'Advanced Dental Clinic',
                        'title' => 'Your Beautiful Smile Matters',
                        'description' => 'Advanced dental clinic offering all cosmetic and therapeutic dental services with latest technology'
                    ]
                ],
                'branches' => [
                    [
                        'name_ar' => 'فرع دبي',
                        'name_en' => 'Dubai Branch',
                        'address' => 'شارع الشيخ زايد، دبي',
                        'lat' => 25.2048,
                        'lng' => 55.2708,
                        'phone' => '+97141234567',
                        'working_hours_json' => [
                            'sunday' => ['08:00', '20:00'],
                            'monday' => ['08:00', '20:00'],
                            'tuesday' => ['08:00', '20:00'],
                            'wednesday' => ['08:00', '20:00'],
                            'thursday' => ['08:00', '20:00'],
                            'friday' => ['09:00', '17:00'],
                            'saturday' => ['09:00', '15:00']
                        ],
                        'is_main' => true,
                        'is_active' => true
                    ]
                ]
            ]
        ];

        foreach ($providers as $providerData) {
            $translations = $providerData['translations'];
            $branches = $providerData['branches'];
            unset($providerData['translations']);
            unset($providerData['branches']);

            $country = \App\Models\Country::where('phone_code', $providerData['country_code'])->first();

            if ($country) {
                $providerData['country_id'] = $country->id;
                unset($providerData['country_code']);

                $provider = Provider::updateOrCreate(
                    ['phone' => $providerData['phone']],
                    $providerData
                );

                // Create translations
                foreach ($translations as $locale => $translation) {
                    ProviderTranslation::updateOrCreate(
                        [
                            'provider_id' => $provider->id,
                            'local' => $locale
                        ],
                        $translation
                    );
                }

                // Create branches
                foreach ($branches as $branchData) {
                    $city = City::whereHas('translations', function($query) use ($branchData) {
                        $query->where('name', 'like', '%' . $branchData['name_en'] . '%');
                    })->first();

                    if ($city) {
                        ProviderBranch::updateOrCreate(
                            [
                                'provider_id' => $provider->id,
                                'city_id' => $city->id
                            ],
                            [
                                'provider_id' => $provider->id,
                                'country_id' => $country->id,
                                'governorate_id' => $city->governorate_id,
                                'city_id' => $city->id,
                                'name_ar' => $branchData['name_ar'],
                                'name_en' => $branchData['name_en'],
                                'address' => $branchData['address'],
                                'lat' => $branchData['lat'],
                                'lng' => $branchData['lng'],
                                'phone' => $branchData['phone'],
                                'working_hours_json' => $branchData['working_hours_json'],
                                'is_main' => $branchData['is_main'],
                                'is_active' => $branchData['is_active']
                            ]
                        );
                    }
                }
            }
        }
    }
}
