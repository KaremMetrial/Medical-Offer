<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\MemberPlan;
use App\Models\PlanTranslation;

class MemberPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // User plans
            [
                'price' => 0.00,
                'duration_days' => 30,
                'features_json' => ['basic_search', 'view_offers', 'basic_reviews'],
                'is_active' => true,
                'is_provider' => false,
                'translations' => [
                    'ar' => [
                        'name' => 'الخطة المجانية',
                        'label' => 'مجاني'
                    ],
                    'en' => [
                        'name' => 'Free Plan',
                        'label' => 'Free'
                    ]
                ]
            ],
            [
                'price' => 29.99,
                'duration_days' => 30,
                'features_json' => ['basic_search', 'view_offers', 'basic_reviews', 'favorite_offers', 'premium_reviews', 'discount_notifications'],
                'is_active' => true,
                'is_provider' => false,
                'translations' => [
                    'ar' => [
                        'name' => 'الخطة المميزة',
                        'label' => 'شهري'
                    ],
                    'en' => [
                        'name' => 'Premium Plan',
                        'label' => 'Monthly'
                    ]
                ]
            ],
            [
                'price' => 299.99,
                'duration_days' => 365,
                'features_json' => ['basic_search', 'view_offers', 'basic_reviews', 'favorite_offers', 'premium_reviews', 'discount_notifications', 'priority_support', 'exclusive_offers'],
                'is_active' => true,
                'is_provider' => false,
                'translations' => [
                    'ar' => [
                        'name' => 'الخطة السنوية',
                        'label' => 'سنوي'
                    ],
                    'en' => [
                        'name' => 'Annual Plan',
                        'label' => 'Annual'
                    ]
                ]
            ],

            // Provider plans
            [
                'price' => 99.99,
                'duration_days' => 30,
                'features_json' => ['basic_listing', 'view_analytics', 'basic_support'],
                'is_active' => true,
                'is_provider' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'خطة المزود الأساسية',
                        'label' => 'شهري'
                    ],
                    'en' => [
                        'name' => 'Basic Provider Plan',
                        'label' => 'Monthly'
                    ]
                ]
            ],
            [
                'price' => 799.99,
                'duration_days' => 365,
                'features_json' => ['basic_listing', 'view_analytics', 'basic_support', 'premium_listing', 'advanced_analytics', 'priority_support', 'marketing_tools'],
                'is_active' => true,
                'is_provider' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'خطة المزود المميزة',
                        'label' => 'سنوي'
                    ],
                    'en' => [
                        'name' => 'Premium Provider Plan',
                        'label' => 'Annual'
                    ]
                ]
            ]
        ];

        foreach ($plans as $planData) {
            $translations = $planData['translations'];
            unset($planData['translations']);

            $plan = MemberPlan::updateOrCreate(
                [
                    'price' => $planData['price'],
                    'duration_days' => $planData['duration_days'],
                    'is_provider' => $planData['is_provider']
                ],
                $planData
            );

            foreach ($translations as $locale => $translation) {
                PlanTranslation::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'local' => $locale
                    ],
                    $translation
                );
            }
        }
    }
}
