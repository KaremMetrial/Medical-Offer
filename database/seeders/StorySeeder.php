<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\Provider;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = Provider::where('status', 'active')->limit(5)->get();
        $countries = Country::all();

        if ($providers->isEmpty()) {
            return;
        }

        foreach ($providers as $provider) {
            // Create a story for each provider
            $story = Story::create([
                'provider_id' => $provider->id,
                'story_type' => 'image',
                'media_url' => 'stories/story_' . $provider->id . '.jpg',
                'expiry_time' => Carbon::now()->addHours(24),
            ]);

            // Link story to all countries to ensure it shows up everywhere
            if ($countries->isNotEmpty()) {
                $story->countries()->attach($countries->pluck('id'));
            }
        }
    }
}
