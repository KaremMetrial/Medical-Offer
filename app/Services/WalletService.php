<?php

namespace App\Services;

use App\Models\User;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
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
                'type' => WalletTransactionType::CREDIT,
                'status' => WalletTransactionStatus::SUCCESS,
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
                'type' => WalletTransactionType::DEBIT,
                'status' => WalletTransactionStatus::SUCCESS,
                'amount' => $amount,
                'balance_after' => $user->balance,
                'description' => $description,
                'reference' => $reference,
            ]);
        });
    }

    /**
     * Initiate a wallet top-up request.
     */
    public function initiateTopUp(User $user, float $amountInDisplayCurrency): array
    {
        $countryContext = app(\App\Services\CountryContext::class);
        $countryRepo = app(\App\Repositories\Contracts\CountryRepositoryInterface::class);
        $currencyService = app(\App\Services\CurrencyService::class);
        $mfService = app(\App\Services\Payments\MyFatoorahService::class);

        $userCountry = $countryContext->getCountry() ?:
            $user->country ?: ($user->country_id ? \App\Models\Country::find($user->country_id) : $countryRepo->getDefaultCountry());

        // // Force EGP for MyFatoorah display as requested by user
        $userCurrency = $userCountry->currency_unit ?: 'SAR';
        $systemBase = config('settings.currency.system_base', 'USD');

        // 1. Create PENDING Wallet Transaction (so we can track it)
        $transaction = $this->repository->createTransaction([
            'user_id' => $user->id,
            'type' => WalletTransactionType::CREDIT,
            'status' => WalletTransactionStatus::PENDING,
            'amount' => $currencyService->convert($amountInDisplayCurrency, $userCurrency, $systemBase), // Convert back to base currency
            'balance_after' => $user->balance, // No change yet
            'description' => __('message.wallet.topup_description'),
            'metadata' => [
                'display_amount' => $amountInDisplayCurrency,
                'display_currency' => $userCurrency,
            ]
        ]);


        // 2. Create MyFatoorah Invoice
        $curlData = [
            'CustomerName'       => $user->name ?: 'Customer',
            'InvoiceValue'       => round($amountInDisplayCurrency, 2),
            'DisplayCurrencyIso' => $userCurrency,
            'CustomerEmail'      => $user->email ?: 'test@test.com',
            'CallBackUrl'        => route('myfatoorah.callback'), // Use the same callback
            'ErrorUrl'           => route('myfatoorah.callback'),
            'Language'           => app()->getLocale(),
            'CustomerReference'  => 'WT-' . $transaction->id, // Mark it as Wallet Top-up
            'SourceInfo'         => config('app.name') . ' ' . app()::VERSION . ' - Wallet Top-up',
        ];

        try {
            $payment = $mfService->createInvoice($curlData);

            $transaction->update([
                'provider_ref' => $payment['invoiceId'] ?? null,
            ]);

            return [
                'success' => true,
                'redirect_url' => route('myfatoorah.gateway', ['url' => $payment['invoiceURL']]),
                'transaction_id' => $transaction->id
            ];

        } catch (\Exception $e) {
            $transaction->update(['status' => WalletTransactionStatus::FAILED]);
            throw $e;
        }
    }

    /**
     * Complete a pending wallet top-up.
     */
    public function completeTopUp($transactionId, $providerRef, $amountPaid): bool
    {
        $transaction = WalletTransaction::find($transactionId);

        if (!$transaction || $transaction->status === WalletTransactionStatus::SUCCESS) {
            return false;
        }

        return DB::transaction(function () use ($transaction, $providerRef, $amountPaid) {
            $user = $transaction->user;

            // Amount in base currency was saved in transaction->amount
            $user->increment('balance', $transaction->amount);
            $user->refresh();

            $transaction->update([
                'status' => WalletTransactionStatus::SUCCESS,
                'balance_after' => $user->balance,
                'provider_ref' => $providerRef,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'actual_amount_paid' => $amountPaid,
                ])
            ]);

            return true;
        });
    }
}
