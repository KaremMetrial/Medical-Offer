<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawalResource;
use App\Http\Resources\PaginationResource;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends BaseController
{
    public function __construct(
        protected WithdrawalService $withdrawalService
    ) {}

    /**
     * Display a listing of the user's withdrawals.
     */
    public function index()
    {
        $user = Auth::user();
        $withdrawals = $user->withdrawals()->latest()->paginate(15);

        return $this->successResponse([
            'withdrawal_list' =>  WithdrawalResource::collection($withdrawals),
            'pagination' => new PaginationResource($withdrawals)
        ]);
    }

    /**
     * Store a newly created withdrawal request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100', // Minimum withdrawal amount e.g. 100
            'method' => 'required|string',
            'payment_details' => 'required|array',
        ]);

        $user = Auth::user();
        
        // 1. Get the current country context and its currency unit
        $country = app(\App\Services\CountryContext::class)->getCountry();
        $unit = $country?->currency_unit ?? 'EGP';
        $systemBase = config('settings.currency.system_base', 'USD');

        // 2. Convert to system base (USD) for checking the user balance in wallet
        $amountInBase = app(\App\Services\CurrencyService::class)->convert($request->amount, $unit, $systemBase);

        if ($user->balance < $amountInBase) {
            return $this->errorResponse(__('message.insufficient_balance'), 422);
        }


        try {
            $withdrawal = $this->withdrawalService->requestWithdrawal(
                $user,
                $request->amount,
                $request->method,
                $request->payment_details
            );

            return $this->successResponse([
                'withdrawal' => new WithdrawalResource($withdrawal)
            ], __('message.withdrawal_request_submitted_successfully'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified withdrawal.
     */
    public function show($id)
    {
        $user = Auth::user();
        $withdrawal = $user->withdrawals()->findOrFail($id);

        return $this->successResponse([
            'withdrawal' => new WithdrawalResource($withdrawal)
        ]);
    }
}
