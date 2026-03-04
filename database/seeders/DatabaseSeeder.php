<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed countries first (foundation data)
        $this->call(CountrySeeder::class);

        // Seed governorates and cities (depends on countries)
        $this->call(GovernorateSeeder::class);
        $this->call(CitySeeder::class);

        // Seed categories (independent)
        $this->call(CategorySeeder::class);

        // Seed member plans (independent)
        $this->call(MemberPlanSeeder::class);

        // Seed users (independent)
        $this->call(UserSeeder::class);

        // Seed providers (depends on countries, cities)
        $this->call(ProviderSeeder::class);

        // Seed offers (depends on providers, categories)
        $this->call(OfferSeeder::class);

        // Seed banners (depends on offers, providers, categories)
        $this->call(BannerSeeder::class);

        // Seed user-provider relationships (depends on users and providers)
        $this->call(UserProviderSeeder::class);

        // Note: Favorites, Reviews, OfferViews, and Payments are not seeded
        // as they represent user-generated content and should be created
        // through the application's normal workflow
    }
}
