<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = \App\Models\Provider::pluck('id')->toArray();
        if (empty($providers)) {
            $this->command->error('No providers found. Please seed providers first.');
            return;
        }

        foreach (\App\Models\User::whereNull('parent_user_id')->with('children')->get() as $user) {
            $providerId = $providers[array_rand($providers)];
            $providerOffers = \App\Models\Offer::where('provider_id', $providerId)->get()
                ->map(fn($o) => $o->name)->toArray();
            
            $services = !empty($providerOffers) 
                ? (array)array_intersect_key($providerOffers, array_flip((array)array_rand($providerOffers, min(count($providerOffers), rand(1, 2)))))
                : ['General Checkup'];

            // Create a visit for the user themselves
            \App\Models\Visit::create([
                'user_id'         => $user->id,
                'provider_id'     => $providerId,
                'visit_date'      => now()->subDays(rand(1, 14)),
                'services'        => array_values($services),
                'paid_amount'     => rand(100, 1000),
                'discount_amount' => rand(10, 100),
                'status'          => 'completed',
                'comment'         => 'Seeded for testing (parent)',
            ]);

            // If user has companions, create visits for them too
            if ($user->children->isNotEmpty()) {
                foreach ($user->children as $companion) {
                    $cProviderId = $providers[array_rand($providers)];
                    $cProviderOffers = \App\Models\Offer::where('provider_id', $cProviderId)->get()
                        ->map(fn($o) => $o->name)->toArray();
                    
                    $cServices = !empty($cProviderOffers)
                        ? (array)array_intersect_key($cProviderOffers, array_flip((array)array_rand($cProviderOffers, min(count($cProviderOffers), rand(1, 2)))))
                        : ['Routine Checkup'];

                    \App\Models\Visit::create([
                        'user_id'         => $user->id,
                        'companion_id'    => $companion->id,
                        'provider_id'     => $cProviderId,
                        'visit_date'      => now()->subDays(rand(1, 14)),
                        'services'        => array_values($cServices),
                        'paid_amount'     => rand(50, 500),
                        'discount_amount' => rand(5, 50),
                        'status'          => 'completed',
                        'comment'         => 'Seeded for testing (companion)',
                    ]);
                }
            }
        }
    }
}
