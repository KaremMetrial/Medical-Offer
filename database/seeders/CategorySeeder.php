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
        $categories = [
            // Parent categories
            [
                'icon' => 'icons/healthcare.png',
                'parent_id' => null,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'الرعاية الصحية'],
                    'en' => ['name' => 'Healthcare']
                ]
            ],
            [
                'icon' => 'icons/cosmetics.png',
                'parent_id' => null,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'التجميل'],
                    'en' => ['name' => 'Cosmetics']
                ]
            ],
            [
                'icon' => 'icons/dental.png',
                'parent_id' => null,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    'ar' => ['name' => 'طب الأسنان'],
                    'en' => ['name' => 'Dental Care']
                ]
            ],
            [
                'icon' => 'icons/fitness.png',
                'parent_id' => null,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 4,
                'translations' => [
                    'ar' => ['name' => 'اللياقة البدنية'],
                    'en' => ['name' => 'Fitness']
                ]
            ],
            [
                'icon' => 'icons/laboratory.png',
                'parent_id' => null,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 5,
                'translations' => [
                    'ar' => ['name' => 'المختبرات'],
                    'en' => ['name' => 'Laboratories']
                ]
            ],

            // Healthcare subcategories
            [
                'icon' => 'icons/general.png',
                'parent_id' => 1, // Healthcare
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'الطب العام'],
                    'en' => ['name' => 'General Medicine']
                ]
            ],
            [
                'icon' => 'icons/pediatrics.png',
                'parent_id' => 1,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'طب الأطفال'],
                    'en' => ['name' => 'Pediatrics']
                ]
            ],
            [
                'icon' => 'icons/dermatology.png',
                'parent_id' => 1,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    'ar' => ['name' => 'الجلدية'],
                    'en' => ['name' => 'Dermatology']
                ]
            ],

            // Cosmetics subcategories
            [
                'icon' => 'icons/skin.png',
                'parent_id' => 2, // Cosmetics
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'عناية البشرة'],
                    'en' => ['name' => 'Skin Care']
                ]
            ],
            [
                'icon' => 'icons/hair.png',
                'parent_id' => 2,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'عناية الشعر'],
                    'en' => ['name' => 'Hair Care']
                ]
            ],
            [
                'icon' => 'icons/plastic.png',
                'parent_id' => 2,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    'ar' => ['name' => 'التجميل الطبي'],
                    'en' => ['name' => 'Medical Aesthetics']
                ]
            ],

            // Dental subcategories
            [
                'icon' => 'icons/teeth.png',
                'parent_id' => 3, // Dental Care
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 1,
                'translations' => [
                    'ar' => ['name' => 'تجميل الأسنان'],
                    'en' => ['name' => 'Dental Aesthetics']
                ]
            ],
            [
                'icon' => 'icons/orthodontics.png',
                'parent_id' => 3,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 2,
                'translations' => [
                    'ar' => ['name' => 'تقويم الأسنان'],
                    'en' => ['name' => 'Orthodontics']
                ]
            ],
            [
                'icon' => 'icons/implants.png',
                'parent_id' => 3,
                'is_show' => true,
                'is_active' => true,
                'sort_order' => 3,
                'translations' => [
                    'ar' => ['name' => 'زراعة الأسنان'],
                    'en' => ['name' => 'Dental Implants']
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $translations = $categoryData['translations'];
            unset($categoryData['translations']);

            $category = Category::updateOrCreate(
                [
                    'parent_id' => $categoryData['parent_id'],
                    'sort_order' => $categoryData['sort_order']
                ],
                $categoryData
            );

            foreach ($translations as $locale => $translation) {
                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'local' => $locale
                    ],
                    $translation
                );
            }
        }
    }
}
