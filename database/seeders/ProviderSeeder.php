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
        // Truncate existing data
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('provider_translations')->truncate();
        \Illuminate\Support\Facades\DB::table('provider_branch_translations')->truncate();
        \Illuminate\Support\Facades\DB::table('provider_branches')->truncate();
        \Illuminate\Support\Facades\DB::table('provider_categories')->truncate();
        \Illuminate\Support\Facades\DB::table('providers')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sections = \App\Models\Section::all()->keyBy('type');
        $categories = \App\Models\Category::all();
        $cities = \App\Models\City::all();
        $countries = \App\Models\Country::all();

        $providers = [
            // Doctors
            [
                'section_type' => \App\Enums\SectionType::DOCTORS,
                'category_keyword' => 'Pediatrics',
                'phone' => '01011111111',
                'experince_years' => 10,
                'ar' => ['name' => 'د. أحمد علي', 'title' => 'استشاري طب الأطفال'],
                'en' => ['name' => 'Dr. Ahmed Ali', 'title' => 'Pediatrics Consultant'],
                'branch' => [
                    'address' => '15 شارع التحرير، وسط البلد، القاهرة',
                    'lat' => 30.0444, 'lng' => 31.2357,
                ],
            ],
            // Medical Centers
            [
                'section_type' => \App\Enums\SectionType::CENTERS,
                'category_keyword' => 'Healthcare',
                'phone' => '01022222222',
                'experince_years' => 20,
                'ar' => ['name' => 'مركز الشفاء الطبي', 'title' => 'رعاية طبية متكاملة'],
                'en' => ['name' => 'Al-Shifa Medical Center', 'title' => 'Integrated Medical Care'],
                'branch' => [
                    'address' => '42 شارع الجيزة، الدقي، الجيزة',
                    'lat' => 30.0626, 'lng' => 31.2099,
                ],
            ],
            // Labs
            [
                'section_type' => \App\Enums\SectionType::LABS,
                'category_keyword' => 'Laboratories',
                'phone' => '01033333333',
                'experince_years' => 15,
                'ar' => ['name' => 'معامل البرج', 'title' => 'دقة في التحاليل'],
                'en' => ['name' => 'Al-Borg Labs', 'title' => 'Precision in Analysis'],
                'branch' => [
                    'address' => '7 شارع مصدق، المهندسين، الجيزة',
                    'lat' => 30.0561, 'lng' => 31.2084,
                ],
            ],
            // Pharmacies
            [
                'section_type' => \App\Enums\SectionType::PHARMACIES,
                'category_keyword' => 'Pharmacies',
                'phone' => '01044444444',
                'experince_years' => 25,
                'ar' => ['name' => 'صيدليات العزبي', 'title' => 'دواءك في أمان'],
                'en' => ['name' => 'El-Ezaby Pharmacies', 'title' => 'Your Medicine is Safe'],
                'branch' => [
                    'address' => '120 شارع عباس العقاد، مدينة نصر، القاهرة',
                    'lat' => 30.0671, 'lng' => 31.3260,
                ],
            ],
        ];

        foreach ($providers as $data) {
            $section = $sections[$data['section_type']->value] ?? null;
            $category = $categories->filter(fn($c) => str_contains($c->name, $data['category_keyword']))->first();
            $country = $countries->first();
            $city = $cities->first();

            $provider = Provider::create([
                'section_id' => $section?->id,
                'country_id' => $country?->id,
                'phone' => $data['phone'],
                'experince_years' => $data['experince_years'],
                'status' => 'active',
                'is_varified' => true,
                'logo' => 'providers/default-logo.png',
                'cover' => 'providers/default-cover.jpg',
            ]);

            // Translations
            foreach (['ar', 'en'] as $lang) {
                \App\Models\ProviderTranslation::create([
                    'provider_id' => $provider->id,
                    'local' => $lang,
                    'name' => $data[$lang]['name'],
                    'title' => $data[$lang]['title'],
                    'description' => $data[$lang]['name'] . ' provides high quality services in ' . $data['category_keyword'],
                ]);
            }

            // Category link
            if ($category) {
                $provider->categories()->attach($category->id);
            }

            // Branch link
            if ($city) {
                $branch = \App\Models\ProviderBranch::create([
                    'provider_id' => $provider->id,
                    'country_id' => $country?->id,
                    'governorate_id' => $city->governorate_id,
                    'city_id' => $city->id,
                    'address' => $data['branch']['address'] ?? 'Cairo, Egypt',
                    'lat' => $data['branch']['lat'] ?? 30.0444,
                    'lng' => $data['branch']['lng'] ?? 31.2357,
                    'is_main' => true,
                    'is_active' => true,
                ]);

                \Illuminate\Support\Facades\DB::table('provider_branch_translations')->insert([
                    ['provider_branch_id' => $branch->id, 'local' => 'ar', 'name' => 'فرع رئيسي', 'created_at' => now(), 'updated_at' => now()],
                    ['provider_branch_id' => $branch->id, 'local' => 'en', 'name' => 'Main Branch', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        }
    }
}
