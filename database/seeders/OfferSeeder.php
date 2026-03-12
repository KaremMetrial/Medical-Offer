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
        // Truncate existing data
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('offer_translations')->truncate();
        \Illuminate\Support\Facades\DB::table('offer_images')->truncate();
        \Illuminate\Support\Facades\DB::table('offer_plan_discounts')->truncate();
        \Illuminate\Support\Facades\DB::table('offers')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $providers = Provider::with('translations')->get()->keyBy(fn($p) => $p->phone);
        $categories = Category::with('translations')->get();
        $plans = MemberPlan::all();

        $offerTemplates = [
            // === Doctor Offers ===
            [
                'provider_phone' => '01011111111',
                'category_keyword' => 'Pediatrics',
                'discount' => 100,
                'ar' => ['name' => 'كشف أطفال مجاني', 'description' => 'كشف مجاني لأول مرة بمناسبة الافتتاح', 'terms' => 'مرة واحدة فقط لكل مريض'],
                'en' => ['name' => 'Free Pediatrics Checkup', 'description' => 'Free first-time consultation for new patients', 'terms' => 'Valid once per patient'],
                'show_home' => true, 'sort' => 1,
            ],
            [
                'provider_phone' => '01011111111',
                'category_keyword' => 'Pediatrics',
                'discount' => 30,
                'ar' => ['name' => '30% خصم على التطعيمات', 'description' => 'خصم على جميع التطعيمات للأطفال دون سن 5 سنوات', 'terms' => 'يشمل الأطفال من عمر 0 إلى 5 سنوات'],
                'en' => ['name' => '30% Off Vaccinations', 'description' => 'Discount on all vaccinations for children under 5', 'terms' => 'For children aged 0-5 years'],
                'show_home' => true, 'sort' => 2,
            ],

            // === Medical Center Offers ===
            [
                'provider_phone' => '01022222222',
                'category_keyword' => 'Healthcare',
                'discount' => 50,
                'ar' => ['name' => 'فحص شامل بخصم 50%', 'description' => 'فحص طبي شامل يغطي كافة الأجهزة', 'terms' => 'يُطبق على حجوزات الفحص الشامل فقط'],
                'en' => ['name' => 'Full Checkup 50% OFF', 'description' => 'Comprehensive screening covering all body systems', 'terms' => 'Applies to full body checkup package only'],
                'show_home' => true, 'sort' => 3,
            ],
            [
                'provider_phone' => '01022222222',
                'category_keyword' => 'Dermatology',
                'discount' => 25,
                'ar' => ['name' => 'خصم 25% على طب الجلد', 'description' => 'أحدث علاجات مشاكل البشرة مع خصم حصري', 'terms' => 'ساري على الجلسات العلاجية'],
                'en' => ['name' => '25% Off Dermatology', 'description' => 'Expert dermatology treatments at a discounted rate', 'terms' => 'Valid on treatment sessions'],
                'show_home' => true, 'sort' => 4,
            ],

            // === Lab Offers ===
            [
                'provider_phone' => '01033333333',
                'category_keyword' => 'Laboratories',
                'discount' => 100,
                'ar' => ['name' => 'تحليل سكر مجاني', 'description' => 'تحليل سكر صائم مجاناً مع أي باقة تحاليل', 'terms' => 'مع شراء باقة تحاليل فقط'],
                'en' => ['name' => 'Free Glucose Test', 'description' => 'Free fasting glucose test with any lab package', 'terms' => 'With purchase of a lab package'],
                'show_home' => true, 'sort' => 5,
            ],
            [
                'provider_phone' => '01033333333',
                'category_keyword' => 'Radiology',
                'discount' => 40,
                'ar' => ['name' => '40% خصم على الأشعة', 'description' => 'خصم على جميع أنواع الأشعة', 'terms' => 'ساري على أشعة الصدر والبطن'],
                'en' => ['name' => '40% Off X-Ray', 'description' => 'Discount on all types of radiology scans', 'terms' => 'Valid on chest and abdominal x-rays'],
                'show_home' => true, 'sort' => 6,
            ],

            // === Pharmacy Offers ===
            [
                'provider_phone' => '01044444444',
                'category_keyword' => 'Pharmacies',
                'discount' => 10,
                'ar' => ['name' => 'خصم 10% على مستحضرات التجميل', 'description' => 'خصم فوري على جميع المستحضرات المستوردة', 'terms' => 'لا يُجمع مع عروض أخرى'],
                'en' => ['name' => '10% OFF Cosmetics', 'description' => 'Instant discount on all imported cosmetics', 'terms' => 'Cannot be combined with other offers'],
                'show_home' => true, 'sort' => 7,
            ],
            [
                'provider_phone' => '01044444444',
                'category_keyword' => 'Pharmacies',
                'discount' => 15,
                'ar' => ['name' => '15% على الفيتامينات', 'description' => 'خصم على جميع المكملات الغذائية والفيتامينات', 'terms' => 'ساري على الكميات من 2 عبوات فأكثر'],
                'en' => ['name' => '15% Off Vitamins', 'description' => 'Discount on all dietary supplements and vitamins', 'terms' => 'Valid on purchases of 2+ units'],
                'show_home' => false, 'sort' => 8,
            ],
        ];

        foreach ($offerTemplates as $template) {
            $provider = $providers->get($template['provider_phone']);
            $category = $categories->filter(fn($c) => str_contains($c->name ?? '', $template['category_keyword']))->first();

            if (!$provider || !$category) {
                // Try parent category fallback
                $category = $categories->filter(fn($c) => str_contains($c->name ?? '', explode(' ', $template['category_keyword'])[0]))->first();
            }

            if (!$provider) continue;

            $offer = Offer::create([
                'provider_id' => $provider->id,
                'category_id' => $category?->id ?? $categories->first()->id,
                'discount_percent' => $template['discount'],
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(30),
                'status' => 'published',
                'show_in_home' => $template['show_home'],
                'sort_order' => $template['sort'],
            ]);

            foreach (['ar', 'en'] as $lang) {
                OfferTranslation::create([
                    'offer_id' => $offer->id,
                    'local' => $lang,
                    'name' => $template[$lang]['name'],
                    'description' => $template[$lang]['description'],
                    'terms' => $template[$lang]['terms'],
                ]);
            }

            OfferImage::create([
                'offer_id' => $offer->id,
                'path' => 'offers/default-offer.jpg',
                'type' => 'image',
            ]);

            foreach ($plans as $plan) {
                OfferPlanDiscount::create([
                    'offer_id' => $offer->id,
                    'plan_id' => $plan->id,
                    'discount_percent' => min($template['discount'] + 5, 100),
                ]);
            }
        }
    }
}
