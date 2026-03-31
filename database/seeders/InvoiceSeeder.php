<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\MemberPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to have invoices
        $users = User::where('role', 'user')->take(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('No users found to seed invoices for.');
            return;
        }

        // Get some plans
        $plans = MemberPlan::all();
        if ($plans->isEmpty()) {
            $this->command->warn('No member plans found to seed subscriptions for.');
            return;
        }

        foreach ($users as $user) {
            // Create 1-3 subscriptions per user for history
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $plan = $plans->random();
                $isCurrent = ($i === 0);
                
                $startAt = $isCurrent 
                    ? Carbon::now()->subDays(rand(0, 30)) 
                    : Carbon::now()->subMonths($i+1)->subDays(rand(0, 30));
                
                $durationDays = (int)($plan->duration_days ?: 365);
                $endAt = (clone $startAt)->addDays($durationDays);
                
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'status' => $isCurrent ? 'active' : 'expired',
                    'payment_status' => 'paid',
                ]);

                // Update user member_id if current
                if ($isCurrent) {
                    $user->update([
                        'member_id' => 'GM-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($subscription->id, 4, '0', STR_PAD_LEFT),
                        'qr_code' => "SUB-" . $user->id . "-" . $subscription->id,
                    ]);
                }

                // Create the Payment/Invoice record
                Payment::create([
                    'payable_type' => Subscription::class,
                    'payable_id' => $subscription->id,
                    'amount' => $plan->price,
                    'method' => 'wallet', // or randomly from Card, etc.
                    'provider_ref' => 'REF-' . Str::random(10),
                    'status' => 'paid',
                    'created_at' => $startAt,
                ]);
            }
        }

        $this->command->info('Invoice (Payment) records seeded successfully.');
    }
}
