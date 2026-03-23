<?php

namespace App\Services;

use App\Enums\WithdrawalStatus;
use App\Enums\WalletTransactionType;
use App\Models\User;
use App\Models\Withdrawal;
use App\Repositories\Contracts\WithdrawalRepositoryInterface;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public function __construct(
        protected WithdrawalRepositoryInterface $repository,
        protected WalletService $walletService,
        protected CurrencyService $currencyService,
        protected CountryContext $countryContext
    ) {}

    /**
     * Request a new withdrawal.
     */
    public function requestWithdrawal(User $user, float $amount, string $method, array $paymentDetails): Withdrawal
    {
        // 1. Get the current country context (from header country id)
        $country = $this->countryContext->getCountry();
        $unit = $country?->currency_unit ?? 'EGP'; // Default to EGP if not specified (consistent with existing table logic)
        $systemBase = config('settings.currency.system_base', 'USD');

        // 2. Convert the requested amount from local currency to system base (USD)
        $amountInBase = $this->currencyService->convert($amount, $unit, $systemBase);

        // Net amount calculation (could add logic here for fees if needed)
        // Note: Everything in the database is stored in USD
        $fee = 0;
        $netAmountInBase = $amountInBase - $fee;

        return DB::transaction(function () use ($user, $amountInBase, $fee, $netAmountInBase, $method, $paymentDetails) {
            // 3. Create the withdrawal request in base currency (USD)
            $withdrawal = $this->repository->create([
                'user_id' => $user->id,
                'amount' => $amountInBase,
                'fee' => $fee,
                'net_amount' => $netAmountInBase,
                'status' => WithdrawalStatus::PENDING,
                'method' => $method,
                'payment_details' => $paymentDetails,
            ]);

            // 4. Debit the user's wallet immediately in base currency (USD)
            $description = "Withdrawal request #{$withdrawal->id}";
            $this->walletService->debit($user, $amountInBase, $description, "WD-{$withdrawal->id}");

            return $withdrawal;
        });
    }


    /**
     * Approve a withdrawal.
     */
    public function approveWithdrawal(Withdrawal $withdrawal): bool
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING) {
            throw new \Exception(__('message.withdrawal_not_pending'));
        }

        return $withdrawal->update([
            'status' => WithdrawalStatus::APPROVED,
        ]);
    }

    /**
     * Reject a withdrawal and credit the amount back to the user.
     */
    public function rejectWithdrawal(Withdrawal $withdrawal, string $reason): bool
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING && $withdrawal->status !== WithdrawalStatus::APPROVED) {
            throw new \Exception(__('message.withdrawal_cannot_be_rejected'));
        }

        return DB::transaction(function () use ($withdrawal, $reason) {
            $user = $withdrawal->user;

            // 1. Update status
            $withdrawal->update([
                'status' => WithdrawalStatus::REJECTED,
                'rejection_reason' => $reason,
            ]);

            // 2. Credit the amount back to the user's wallet
            $description = "Withdrawal #{$withdrawal->id} rejected: {$reason}";
            $this->walletService->credit($user, $withdrawal->amount, $description, "WD-REJ-{$withdrawal->id}");

            return true;
        });
    }

    /**
     * Mark withdrawal as completed (payment confirmed).
     */
    public function completeWithdrawal(Withdrawal $withdrawal, string $referenceId = null): bool
    {
        if ($withdrawal->status !== WithdrawalStatus::APPROVED) {
            throw new \Exception(__('message.withdrawal_not_approved'));
        }

        return $withdrawal->update([
            'status' => WithdrawalStatus::COMPLETED,
            'reference_id' => $referenceId,
        ]);
    }
}
