<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function __construct(
        protected WalletTransactionRepositoryInterface $repository
    ) {}

    /**
     * Get user wallet balance converted to display currency.
     * Uses x-cuntry-id header if provided, otherwise falls back to user's country.
     */
    public function getBalance(User $user): array
    {
        $currencyService = app(CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'USD');

        // Check if x-cuntry-id header is provided for currency override
        $countryContext = app(\App\Services\CountryContext::class);
        $overrideCountryId = $countryContext->getCountryId();
        
        // Use override country if provided, otherwise use user's country
        $country = null;
        if ($overrideCountryId) {
            $country = \App\Models\Country::find($overrideCountryId);
        }
        
        // Fall back to user's country if override not available or invalid
        if (!$country) {
            $country = $user->country;
        }

        $symbol = $country?->currency_symbol ?? '$';
        $unit = $country?->currency_unit ?? 'USD';

        $balanceInLocal = $currencyService->convert((float) $user->balance, $systemBase, $unit);
        $factor = (float)($country?->currency_factor ?: 1);
        $decimals = $factor == 1000 ? 3 : 2;

        return [
            'balance' => round($balanceInLocal, $decimals),
            'currency_symbol' => $symbol,
            'currency_unit' => $unit,
        ];
    }

    /**
     * Get recent transactions for the user.
     */
    public function getRecentTransactions(User $user, int $limit = 5)
    {
        return $this->repository->getRecentByUser($user->id, $limit);
    }

    /**
     * Get all transactions paginated.
     */
    public function getAllTransactions(User $user, int $perPage = 15)
    {
        return $this->repository->getAllByUserPaginated($user->id, $perPage);
    }

    /**
     * Add credit to the user's wallet.
     */
    public function credit(User $user, float $amount, string $description = '', string $reference = ''): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference) {
            $user->increment('balance', $amount);
            $user->refresh();

            return $this->repository->createTransaction([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'description' => $description,
                'reference' => $reference,
            ]);
        });
    }

    /**
     * Deduct from the user's wallet.
     */
    public function debit(User $user, float $amount, string $description = '', string $reference = ''): WalletTransaction
    {
        if ($user->balance < $amount) {
            throw new \Exception(__('message.insufficient_balance'));
        }

        return DB::transaction(function () use ($user, $amount, $description, $reference) {
            $user->decrement('balance', $amount);
            $user->refresh();

            return $this->repository->createTransaction([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'description' => $description,
                'reference' => $reference,
            ]);
        });
    }
}
