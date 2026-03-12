<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\SectionTranslation;
use App\Models\Category;
use App\Models\Provider;
use App\Enums\SectionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing sections
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('section_translations')->truncate();
        DB::table('sections')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sections = [
            [
                'type' => SectionType::DOCTORS,
                'is_active' => true,
                'sort_order' => 1,
                'ar' => 'أطباء',
                'en' => 'Doctors',
            ],
            [
                'type' => SectionType::CENTERS,
                'is_active' => true,
                'sort_order' => 2,
                'ar' => 'مراكز طبية',
                'en' => 'Medical Centers',
            ],
            [
                'type' => SectionType::LABS,
                'is_active' => true,
                'sort_order' => 3,
                'ar' => 'تحاليل وأشعة',
                'en' => 'Labs & Radiology',
            ],
            [
                'type' => SectionType::PHARMACIES,
                'is_active' => true,
                'sort_order' => 4,
                'ar' => 'صيدليات',
                'en' => 'Pharmacies',
            ],
        ];

        // Create or update sections
        foreach ($sections as $data) {
            $section = Section::updateOrCreate(
                ['type' => $data['type']],
                [
                    'is_active' => $data['is_active'],
                    'sort_order' => $data['sort_order'],
                    'icon' => 'sections/' . strtolower($data['type']->value) . '.png'
                ]
            );

            SectionTranslation::updateOrCreate(
                ['section_id' => $section->id, 'local' => 'ar'],
                ['name' => $data['ar']]
            );

            SectionTranslation::updateOrCreate(
                ['section_id' => $section->id, 'local' => 'en'],
                ['name' => $data['en']]
            );
        }

        // Get created sections
        $doctorsSection = Section::where('type', SectionType::DOCTORS)->first();
        $centersSection = Section::where('type', SectionType::CENTERS)->first();
        $labsSection = Section::where('type', SectionType::LABS)->first();
        $pharmaciesSection = Section::where('type', SectionType::PHARMACIES)->first();

        // 1. Link Categories
        // Categories with "مركز" or "مستشفى" go to Centers
        Category::whereHas('translations', function($q) {
            $q->where('name', 'like', '%مركز%')->orWhere('name', 'like', '%مستشفى%')
              ->orWhere('name', 'like', '%Center%')->orWhere('name', 'like', '%Hospital%');
        })->update(['section_id' => $centersSection->id]);

        // Categories with "معمل" or "أشعة" or "تحليل" go to Labs
        Category::whereHas('translations', function($q) {
            $q->where('name', 'like', '%معمل%')->orWhere('name', 'like', '%تحليل%')->orWhere('name', 'like', '%أشعة%')
              ->orWhere('name', 'like', '%Lab%')->orWhere('name', 'like', '%Scan%');
        })->update(['section_id' => $labsSection->id]);

        // Categories with "صيدلية" go to Pharmacies
        Category::whereHas('translations', function($q) {
            $q->where('name', 'like', '%صيدلية%')->orWhere('name', 'like', '%Pharmacy%');
        })->update(['section_id' => $pharmaciesSection->id]);

        // Everything else to Doctors
        Category::whereNull('section_id')->update(['section_id' => $doctorsSection->id]);

        // Ensure categories are active
        Category::query()->update(['is_active' => true, 'is_show' => true]);

        // 2. Link Providers based on their primary category (or split them)
        // For simplicity, we can link providers according to their section_id if not set
        // Or better, update providers to match their categories' sections
        DB::statement("
            UPDATE providers p
            JOIN provider_categories pc ON p.id = pc.provider_id
            JOIN categories c ON pc.category_id = c.id
            SET p.section_id = c.section_id
            WHERE p.section_id IS NULL OR p.section_id = 0
        ");

        // Set remaining to Doctors
        Provider::whereNull('section_id')->update(['section_id' => $doctorsSection->id]);

        // Make providers active and verified
        Provider::query()->update(['status' => 'active', 'is_varified' => true]);

        // 3. Offers - Publish and show in home
        \App\Models\Offer::query()->update([
            'status' => 'published',
            'show_in_home' => true
        ]);
    }
}
