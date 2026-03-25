<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::whereNull('parent_user_id')->limit(5)->get();
        $relationships = \App\Enums\RelationshipType::values();
        $status = \App\Enums\CompanionStatus::APPROVED->value;

        foreach ($users as $parent) {
            for ($i = 1; $i <= 2; $i++) {
                \App\Models\User::updateOrCreate(
                    ['phone' => '01' . rand(500000000, 999999999)],
                    [
                        'name'             => "Companion of {$parent->name} {$i}",
                        'password'         => \Illuminate\Support\Facades\Hash::make('password'),
                        'parent_user_id'   => $parent->id,
                        'relationship'     => $relationships[array_rand($relationships)],
                        'is_active'        => true,
                        'companion_status' => $status,
                        'role'             => 'user',
                    ]
                );
            }
        }
    }
}
