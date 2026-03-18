<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\WalletTransactionResource;
use App\Http\Resources\PaginationResource;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WalletController extends BaseController
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Wallet main page: balance + recent transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $balance = $this->walletService->getBalance($user);
        $recentTransactions = $this->walletService->getRecentTransactions($user);

        return $this->successResponse([
            'balance' => $balance,
            'recent_transactions' => WalletTransactionResource::collection($recentTransactions),
            'labels' => [
                'title' => __('message.wallet.title'),
                'balance_label' => __('message.wallet.balance'),
                'add_balance' => __('message.wallet.add_balance'),
                'withdraw' => __('message.wallet.withdraw'),
                'recent_transactions' => __('message.wallet.recent_transactions'),
                'view_all' => __('message.wallet.view_all'),
                'credit_description' => __('message.wallet.credit_description'),
                'debit_description' => __('message.wallet.debit_description'),
            ],
        ]);
    }

    /**
     * All transactions paginated.
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();
        $transactions = $this->walletService->getAllTransactions($user);

        return $this->successResponse([
            'transactions' => WalletTransactionResource::collection($transactions),
            'pagination' => new PaginationResource($transactions),
            'labels' => [
                'title' => __('message.wallet.title'),
                'credit_description' => __('message.wallet.credit_description'),
                'debit_description' => __('message.wallet.debit_description'),
            ],
        ]);
    }
}
