<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'عروض الربيع المميزة',
                'image_path' => 'banners/spring-offers.jpg',
                'link_type' => 'category',
                'link_id' => 2, // Cosmetics category
                'external_url' => null,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'position' => 'home_top',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'title' => 'فحص طبي مجاني',
                'image_path' => 'banners/free-checkup.jpg',
                'link_type' => 'offer',
                'link_id' => 1, // First offer
                'external_url' => null,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(27),
                'position' => 'home_slider',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'title' => 'خصومات الصيف',
                'image_path' => 'banners/summer-discounts.jpg',
                'link_type' => 'provider',
                'link_id' => 2, // Beauty center
                'external_url' => null,
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(30),
                'position' => 'home_bottom',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'title' => 'خدمات طبية متكاملة',
                'image_path' => 'banners/medical-services.jpg',
                'link_type' => 'external',
                'link_id' => null,
                'external_url' => 'https://example.com/medical-services',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'position' => 'sidebar',
                'is_active' => true,
                'sort_order' => 1
            ]
        ];

        foreach ($banners as $bannerData) {
            Banner::updateOrCreate(
                [
                    'title' => $bannerData['title'],
                    'position' => $bannerData['position']
                ],
                $bannerData
            );
        }
    }
}
