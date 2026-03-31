<?php

namespace App\Console\Commands\Subscriptions;

use Illuminate\Console\Command;
use App\Services\SubscriptionLifecycleService;

class CleanupPendingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:cleanup-pending {--minutes=15 : The time window for pending subscriptions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-cancel pending subscriptions that have exceeded the payment window';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionLifecycleService $service)
    {
        $minutes = $this->option('minutes');
        $this->info("Cleaning up subscriptions older than {$minutes} minutes...");
        
        $count = $service->cleanUpPendingSubscriptions($minutes);
        
        $this->info("Successfully cancelled {$count} subscriptions.");
    }
}
