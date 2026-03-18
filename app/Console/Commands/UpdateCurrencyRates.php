<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-currency-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest currency exchange rates from API and sync with config and database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\CurrencyService $currencyService)
    {
        $this->info('Starting currency rates update...');

        // Force refresh by clearing cache
        \Illuminate\Support\Facades\Cache::forget('currency_rates');

        try {
            $rates = $currencyService->getRates();
            
            if (!empty($rates)) {
                $this->info('Successfully updated currency rates.');
                return self::SUCCESS;
            }
            
            $this->error('Failed to retrieve rates. Check logs for details.');
            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
