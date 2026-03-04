<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\OfferTranslation;
use App\Models\OfferImage;
use App\Models\OfferPlanDiscount;
use App\Models\Provider;
use App\Models\Category;
use App\Models\MemberPlan;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $offers = [
            [
                'provider_phone' => '+966112345678',
                'category_name' => 'General Medicine',
                'discount_percent' => 20,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'status' => 'published',
                'show_in_home' => true,
                'sort_order' => 1,
                'views' => 500,
                'translations' => [
                    'ar' => [
                        'name' => 'فحص طبي شامل',
                        'description' => 'احصل على فحص طبي شامل بخصم 20% لجميع الفئات',
                        'terms' => 'العرض ساري على جميع الفحوصات الطبية الشاملة'
                    ],
                    'en' => [
                        'name' => 'Comprehensive Medical Checkup',
                        'description' => 'Get 20% discount on comprehensive medical checkups for all categories',
                        'terms' => 'Offer valid on all comprehensive medical checkups'
                    ]
                ],
                'images' => [
                    ['path' => 'offers/medical-checkup.jpg', 'type' => 'image', 'sort_order' => 0]
                ],
                'plan_discounts' => [
                    ['plan_name' => 'Premium Plan', 'discount_percent' => 25],
                    ['plan_name' => 'Annual Plan', 'discount_percent' => 30]
                ]
            ],
            [
                'provider_phone' => '+20223456789',
                'category_name' => 'Skin Care',
                'discount_percent' => 35,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(27),
                'status' => 'published',
                'show_in_home' => true,
                'sort_order' => 2,
                'views' => 800,
                'translations' => [
                    'ar' => [
                        'name' => 'علاجات البشرة الفاخرة',
                        'description' => 'خصم 35% على جميع علاجات البشرة الفاخرة',
                        'terms' => 'العرض يشمل جميع علاجات البشرة المقدمة من خبرائنا'
                    ],
                    'en' => [
                        'name' => 'Luxury Skin Treatments',
                        'description' => '35% discount on all luxury skin treatments',
                        'terms' => 'Offer includes all skin treatments provided by our experts'
                    ]
                ],
                'images' => [
                    ['path' => 'offers/skin-treatment.jpg', 'type' => 'image', 'sort_order' => 0]
                ],
                'plan_discounts' => [
                    ['plan_name' => 'Premium Plan', 'discount_percent' => 40],
                    ['plan_name' => 'Annual Plan', 'discount_percent' => 45]
                ]
            ],
            [
                'provider_phone' => '+97141234567',
                'category_name' => 'Dental Aesthetics',
                'discount_percent' => 25,
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(30),
                'status' => 'published',
                'show_in_home' => false,
                'sort_order' => 3,
                'views' => 300,
                'translations' => [
                    'ar' => [
                        'name' => 'تبييض الأسنان الاحترافي',
                        'description' => 'تبييض أسنان احترافي بخصم 25% لفترة محدودة',
                        'terms' => 'العرض ساري على جلسات تبييض الأسنان الاحترافية'
                    ],
                    'en' => [
                        'name' => 'Professional Teeth Whitening',
                        'description' => 'Professional teeth whitening with 25% discount for limited time',
                        'terms' => 'Offer valid on professional teeth whitening sessions'
                    ]
                ],
                'images' => [
                    ['path' => 'offers/teeth-whitening.jpg', 'type' => 'image', 'sort_order' => 0]
                ],
                'plan_discounts' => [
                    ['plan_name' => 'Premium Plan', 'discount_percent' => 30],
                    ['plan_name' => 'Annual Plan', 'discount_percent' => 35]
                ]
            ],
            [
                'provider_phone' => '+966112345678',
                'category_name' => 'Pediatrics',
                'discount_percent' => 15,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'status' => 'published',
                'show_in_home' => false,
                'sort_order' => 4,
                'views' => 200,
                'translations' => [
                    'ar' => [
                        'name' => 'فحص الأطفال الشامل',
                        'description' => 'فحص طبي شامل للأطفال بخصم 15%',
                        'terms' => 'العرض ساري على الفحوصات الطبية للأطفال من سن 0 إلى 12 سنة'
                    ],
                    'en' => [
                        'name' => 'Comprehensive Children Checkup',
                        'description' => 'Comprehensive medical checkup for children with 15% discount',
                        'terms' => 'Offer valid for medical checkups for children aged 0 to 12 years'
                    ]
                ],
                'images' => [
                    ['path' => 'offers/children-checkup.jpg', 'type' => 'image', 'sort_order' => 0]
                ],
                'plan_discounts' => [
                    ['plan_name' => 'Premium Plan', 'discount_percent' => 20],
                    ['plan_name' => 'Annual Plan', 'discount_percent' => 25]
                ]
            ]
        ];

        foreach ($offers as $offerData) {
            $translations = $offerData['translations'];
            $images = $offerData['images'];
            $planDiscounts = $offerData['plan_discounts'];
            unset($offerData['translations']);
            unset($offerData['images']);
            unset($offerData['plan_discounts']);

            $provider = Provider::where('phone', $offerData['provider_phone'])->first();
            $category = Category::whereHas('translations', function($query) use ($offerData) {
                $query->where('name', $offerData['category_name']);
            })->first();

            if ($provider && $category) {
                $offerData['provider_id'] = $provider->id;
                $offerData['category_id'] = $category->id;
                unset($offerData['provider_phone']);
                unset($offerData['category_name']);

                $offer = Offer::updateOrCreate(
                    [
                        'provider_id' => $offerData['provider_id'],
                        'category_id' => $offerData['category_id']
                    ],
                    $offerData
                );

                // Create translations
                foreach ($translations as $locale => $translation) {
                    OfferTranslation::updateOrCreate(
                        [
                            'offer_id' => $offer->id,
                            'local' => $locale
                        ],
                        $translation
                    );
                }

                // Create images
                foreach ($images as $imageData) {
                    OfferImage::updateOrCreate(
                        [
                            'offer_id' => $offer->id,
                            'path' => $imageData['path']
                        ],
                        $imageData
                    );
                }

                // Create plan discounts
                foreach ($planDiscounts as $discountData) {
                    $plan = MemberPlan::join('plan_translations', 'member_plans.id', '=', 'plan_translations.plan_id')
                        ->where('plan_translations.name', $discountData['plan_name'])
                        ->select('member_plans.*')
                        ->first();

                    if ($plan) {
                        OfferPlanDiscount::updateOrCreate(
                            [
                                'offer_id' => $offer->id,
                                'plan_id' => $plan->id
                            ],
                            [
                                'offer_id' => $offer->id,
                                'plan_id' => $plan->id,
                                'discount_percent' => $discountData['discount_percent']
                            ]
                        );
                    }
                }
            }
        }
    }
}
