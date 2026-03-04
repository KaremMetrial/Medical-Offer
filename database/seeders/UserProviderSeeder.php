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
        // Create provider-role users and associate them with providers and branches

        // Provider 1: Health Care Center
        $provider1 = Provider::where('phone', '+966112345678')->first();
        if ($provider1) {
            $this->seedHealthCareCenterUsers($provider1);
        }

        // Provider 2: Beauty & Care Center
        $provider2 = Provider::where('phone', '+20223456789')->first();
        if ($provider2) {
            $this->seedBeautyCenterUsers($provider2);
        }

        // Provider 3: Advanced Dental Clinic
        $provider3 = Provider::where('phone', '+97141234567')->first();
        if ($provider3) {
            $this->seedDentalClinicUsers($provider3);
        }
    }

    private function seedHealthCareCenterUsers($provider)
    {
        // Create provider manager (manages whole provider)
        $manager = User::updateOrCreate(
            ['phone' => '+966509999999'],
            [
                'name' => 'Dr. Ahmed Al-Saud',
                'phone' => '+966509999999',
                'email' => 'dr.ahmed@healthcare.com',
                'password' => bcrypt('provider123'),
                'role' => 'provider',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        // Associate manager with provider (no specific branch)
        $provider->users()->syncWithoutDetaching([$manager->id => ['branch_id' => null]]);

        // Create branch managers
        $branches = $provider->branches;
        foreach ($branches as $index => $branch) {
            $branchManager = User::updateOrCreate(
                ['phone' => '+966508888' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Branch Manager ' . ($index + 1),
                    'phone' => '+966508888' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'email' => 'manager' . ($index + 1) . '@healthcare.com',
                    'password' => bcrypt('branch123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            // Associate branch manager with specific branch
            $provider->users()->syncWithoutDetaching([$branchManager->id => ['branch_id' => $branch->id]]);
        }
    }

    private function seedBeautyCenterUsers($provider)
    {
        // Create provider owner
        $owner = User::updateOrCreate(
            ['phone' => '+201199999999'],
            [
                'name' => 'Sarah Mohamed',
                'phone' => '+201199999999',
                'email' => 'sarah@beautycenter.com',
                'password' => bcrypt('beauty123'),
                'role' => 'provider',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        $provider->users()->syncWithoutDetaching([$owner->id => ['branch_id' => null]]);

        // Create staff for the main branch
        $mainBranch = $provider->branches->first();
        if ($mainBranch) {
            $staff1 = User::updateOrCreate(
                ['phone' => '+201188888888'],
                [
                    'name' => 'Cosmetic Specialist',
                    'phone' => '+201188888888',
                    'email' => 'specialist@beautycenter.com',
                    'password' => bcrypt('staff123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            $staff2 = User::updateOrCreate(
                ['phone' => '+201177777777'],
                [
                    'name' => 'Beauty Consultant',
                    'phone' => '+201177777777',
                    'email' => 'consultant@beautycenter.com',
                    'password' => bcrypt('staff123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            $provider->users()->syncWithoutDetaching([
                $staff1->id => ['branch_id' => $mainBranch->id],
                $staff2->id => ['branch_id' => $mainBranch->id]
            ]);
        }
    }

    private function seedDentalClinicUsers($provider)
    {
        // Create clinic director
        $director = User::updateOrCreate(
            ['phone' => '+971559998888'],
            [
                'name' => 'Dr. Fatima Al-Mansoori',
                'phone' => '+971559998888',
                'email' => 'dr.fatima@dentalclinic.com',
                'password' => bcrypt('dental123'),
                'role' => 'provider',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        $provider->users()->syncWithoutDetaching([$director->id => ['branch_id' => null]]);

        // Create dental specialists for the Dubai branch
        $dubaiBranch = $provider->branches->first();
        if ($dubaiBranch) {
            $specialist1 = User::updateOrCreate(
                ['phone' => '+971557776666'],
                [
                    'name' => 'Orthodontics Specialist',
                    'phone' => '+971557776666',
                    'email' => 'ortho@dentalclinic.com',
                    'password' => bcrypt('ortho123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            $specialist2 = User::updateOrCreate(
                ['phone' => '+971556665555'],
                [
                    'name' => 'Cosmetic Dentist',
                    'phone' => '+971556665555',
                    'email' => 'cosmetic@dentalclinic.com',
                    'password' => bcrypt('cosmetic123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            $assistant = User::updateOrCreate(
                ['phone' => '+971554443333'],
                [
                    'name' => 'Dental Assistant',
                    'phone' => '+971554443333',
                    'email' => 'assistant@dentalclinic.com',
                    'password' => bcrypt('assistant123'),
                    'role' => 'provider',
                    'is_active' => true,
                    'email_verified_at' => now()
                ]
            );

            $provider->users()->syncWithoutDetaching([
                $specialist1->id => ['branch_id' => $dubaiBranch->id],
                $specialist2->id => ['branch_id' => $dubaiBranch->id],
                $assistant->id => ['branch_id' => $dubaiBranch->id]
            ]);
        }
    }
}
