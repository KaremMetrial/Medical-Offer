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

        // Seed sections (foundation data for Categories and Providers)
        $this->call(SectionSeeder::class);

        // Seed categories (depends on sections)
        $this->call(CategorySeeder::class);

        // Seed member plans (independent)
        $this->call(MemberPlanSeeder::class);

        // Seed users (independent)
        $this->call(UserSeeder::class);

        // Seed companions (depends on users)
        $this->call(CompanionSeeder::class);

        // Seed providers (depends on countries, cities)
        $this->call(ProviderSeeder::class);

        // Seed offers (depends on providers, categories)
        $this->call(OfferSeeder::class);

        // Seed banners (depends on offers, providers, categories)
        $this->call(BannerSeeder::class);

        // Seed stories (linked to providers)
        $this->call(StorySeeder::class);

        // Seed user-provider relationships (depends on users and providers)
        $this->call(UserProviderSeeder::class);

        // Seed visits, reviews, and invoices (payments)
        $this->call(VisitSeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(InvoiceSeeder::class);
    }
}
