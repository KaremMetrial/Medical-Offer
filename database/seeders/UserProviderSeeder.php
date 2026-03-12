<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\Seed;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Provider;
use App\Models\ProviderBranch;

class UserProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Link managers to new providers
        $providerMapping = [
            '01011111111' => [
                'name' => 'Dr. Ahmed Manager',
                'email' => 'ahmed.manager@medical.com',
                'phone' => '0501111111',
            ],
            '01022222222' => [
                'name' => 'Al-Shifa Manager',
                'email' => 'shifa.manager@medical.com',
                'phone' => '0501111112',
            ],
            '01033333333' => [
                'name' => 'Al-Borg Manager',
                'email' => 'borg.manager@medical.com',
                'phone' => '0501111113',
            ],
            '01044444444' => [
                'name' => 'El-Ezaby Manager',
                'email' => 'ezaby.manager@medical.com',
                'phone' => '0501111114',
            ],
        ];

        foreach ($providerMapping as $providerPhone => $userData) {
            $provider = Provider::where('phone', $providerPhone)->first();
            if ($provider) {
                $user = User::updateOrCreate(
                    ['phone' => $userData['phone']],
                    [
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => bcrypt('password'),
                        'role' => 'provider',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                // Main Manager
                $provider->users()->syncWithoutDetaching([$user->id => ['branch_id' => null]]);

                // Branch Manager (if branch exists)
                $branch = $provider->branches->first();
                if ($branch) {
                    $branchUser = User::updateOrCreate(
                        ['phone' => '090' . substr($userData['phone'], 3)],
                        [
                            'name' => $userData['name'] . ' Branch',
                            'email' => 'branch.' . $userData['email'],
                            'password' => bcrypt('password'),
                            'role' => 'provider',
                            'is_active' => true,
                            'email_verified_at' => now(),
                        ]
                    );
                    $provider->users()->syncWithoutDetaching([$branchUser->id => ['branch_id' => $branch->id]]);
                }
            }
        }
    }
}
