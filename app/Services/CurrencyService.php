<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected string $apiKey;
    protected string $apiBaseUrl;
    protected string $baseCurrency;

    public function __construct()
    {
        $this->apiKey = config('settings.currency.api_key');
        $this->baseCurrency = config('settings.currency.base', 'USD');
        $this->apiBaseUrl = "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/{$this->baseCurrency}";
    }

    /**
     * Get exchange rates from cache or API.
     */
    public function getRates(): array
    {
        return Cache::remember('currency_rates', now()->addHours(24), function () {
            try {
                if (empty($this->apiKey)) {
                    Log::warning('ExchangeRate API Key is missing. Using config fallback rates.');
                    return $this->getFallbackRates();
                }

                $response = Http::get($this->apiBaseUrl);

                if ($response->successful() && $response->json('result') === 'success') {
                    $rates = $response->json('conversion_rates');
                    
                    // Update the config file with latest rates for next fallback
                    $this->updateFallbackRates($rates);
                    
                    return $rates;
                }

                Log::error('ExchangeRate API error: ' . $response->body());
                return $this->getFallbackRates();
            } catch (\Exception $e) {
                Log::error('CurrencyService Exception: ' . $e->getMessage());
                return $this->getFallbackRates();
            }
        });
    }

    /**
     * Convert an amount from one currency to another.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getRates();

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            Log::error("Currency rate not found: {$from} or {$to}. Available: " . implode(',', array_keys($rates)));
            return $amount; // Return original if conversion fails
        }

        // Formula: Amount * (RateToDestination / RateFromSource)
        // Both rates are relative to USD
        $baseAmount = $amount / $rates[$from];
        return round($baseAmount * $rates[$to], 2);
    }

    /**
     * Update the config/settings.php file with latest rates.
     */
    protected function updateFallbackRates(array $rates): void
    {
        try {
            $configPath = config_path('settings.php');
            if (!file_exists($configPath) || !is_writable($configPath)) {
                return;
            }

            $currentConfig = include $configPath;
            
            // Only update the relevant subset to keep the file clean
            $relevantCurrencies = ['USD', 'SAR', 'EGP', 'KWD', 'AED', 'QAR', 'BHD', 'OMR', 'JOD'];
            $newFallbacks = [];
            foreach ($relevantCurrencies as $currency) {
                if (isset($rates[$currency])) {
                    $newFallbacks[$currency] = $rates[$currency];
                }
            }

            if (empty($newFallbacks)) return;

            $content = file_get_contents($configPath);
            
            // Use regex to find and replace the fallback_rates array block
            // This assumes the format I just created in the previous step
            $pattern = '/\'fallback_rates\'\s*=>\s*\[(.*?)\]/s';
            
            $formattedRates = "\n";
            foreach ($newFallbacks as $code => $rate) {
                $formattedRates .= "            '{$code}' => {$rate},\n";
            }
            $replacement = "'fallback_rates' => [" . $formattedRates . "        ]";

            $newContent = preg_replace($pattern, $replacement, $content);
            
            if ($newContent && $newContent !== $content) {
                file_put_contents($configPath, $newContent);
                // Clear config cache if it was active (optional, usually done manually)
            }
        } catch (\Exception $e) {
            Log::error('Failed to update fallback rates in config: ' . $e->getMessage());
        }
    }

    /**
     * Fallback rates from config.
     */
    protected function getFallbackRates(): array
    {
        return config('settings.currency.fallback_rates', [
            'USD' => 1,
            'SAR' => 3.75,
            'EGP' => 52.35,
            'KWD' => 0.31,
            'AED' => 3.67,
            'QAR' => 3.64,
            'BHD' => 0.38,
            'OMR' => 0.38,
            'JOD' => 0.71,
        ]);
    }
}
