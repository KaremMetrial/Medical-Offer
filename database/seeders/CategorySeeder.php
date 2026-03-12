<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryTranslation;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing categories
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('category_translations')->truncate();
        \Illuminate\Support\Facades\DB::table('categories')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sections = \App\Models\Section::all()->keyBy('type');

        $categories = [
            // Healthcare (Doctors/Centers)
            [
                'id' => 1,
                'icon' => 'icons/healthcare.png',
                'parent_id' => null,
                'section_id' => $sections[\App\Enums\SectionType::DOCTORS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'الرعاية الصحية'],
                    'en' => ['name' => 'Healthcare']
                ]
            ],
            // Dental (Doctors/Centers)
            [
                'id' => 2,
                'icon' => 'icons/dental.png',
                'parent_id' => null,
                'section_id' => $sections[\App\Enums\SectionType::DOCTORS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'طب الأسنان'],
                    'en' => ['name' => 'Dental Care']
                ]
            ],
            // Laboratories
            [
                'id' => 3,
                'icon' => 'icons/laboratory.png',
                'parent_id' => null,
                'section_id' => $sections[\App\Enums\SectionType::LABS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    'ar' => ['name' => 'المختبرات'],
                    'en' => ['name' => 'Laboratories']
                ]
            ],
            // Pharmacies
            [
                'id' => 4,
                'icon' => 'icons/pharmacy.png',
                'parent_id' => null,
                'section_id' => $sections[\App\Enums\SectionType::PHARMACIES->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 4,
                'translations' => [
                    'ar' => ['name' => 'الصيدليات'],
                    'en' => ['name' => 'Pharmacies']
                ]
            ],

            // Subcategories for Healthcare
            [
                'icon' => 'icons/pediatrics.png',
                'parent_id' => 1,
                'section_id' => $sections[\App\Enums\SectionType::DOCTORS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'طب الأطفال'],
                    'en' => ['name' => 'Pediatrics']
                ]
            ],
            [
                'icon' => 'icons/dermatology.png',
                'parent_id' => 1,
                'section_id' => $sections[\App\Enums\SectionType::DOCTORS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'الجلدية'],
                    'en' => ['name' => 'Dermatology']
                ]
            ],

            // Subcategories for Dental
            [
                'icon' => 'icons/teeth.png',
                'parent_id' => 2,
                'section_id' => $sections[\App\Enums\SectionType::DOCTORS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'تجميل الأسنان'],
                    'en' => ['name' => 'Dental Aesthetics']
                ]
            ],
            
            // Subcategories for Labs
            [
                'icon' => 'icons/xray.png',
                'parent_id' => 3,
                'section_id' => $sections[\App\Enums\SectionType::LABS->value]?->id,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'أشعة'],
                    'en' => ['name' => 'Radiology']
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $translations = $categoryData['translations'];
            unset($categoryData['translations']);

            $category = Category::create($categoryData);

            foreach ($translations as $locale => $translation) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'local' => $locale,
                    'name' => $translation['name']
                ]);
            }
        }
    }
}
