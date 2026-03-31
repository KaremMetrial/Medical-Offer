<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Payments\MyFatoorahService;
use App\Services\Payments\PaymentCallbackHandler;
use App\Traits\ApiResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class MyFatoorahController extends Controller
{
    use ApiResponse;

    protected $mfService;

    protected $callbackHandler;

    public function __construct(MyFatoorahService $mfService, PaymentCallbackHandler $callbackHandler)
    {
        $this->mfService = $mfService;
        $this->callbackHandler = $callbackHandler;
    }

    /**
     * Redirect to MyFatoorah invoice URL
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $subscriptionId = $request->get('subscription_id');
            $subscription = \App\Models\Subscription::with(['user', 'plan'])->findOrFail($subscriptionId);

            $user = $subscription->user;
            $plan = $subscription->plan;

            $countryContext = app(\App\Services\CountryContext::class);
            $countryRepo = app(\App\Repositories\Contracts\CountryRepositoryInterface::class);
            $currencyService = app(\App\Services\CurrencyService::class);

            $userCountry = $countryContext->getCountry() ?:
                $user->country ?: ($user->country_id ? \App\Models\Country::find($user->country_id) : $countryRepo->getDefaultCountry());

            // Force EGP for MyFatoorah display as requested by user
            $userCurrency = 'EGP';
            $systemBase = config('settings.currency.system_base', 'USD');
            $planPriceSource = config('settings.currency.base', 'USD');

            $effectivePrice = $currencyService->convert($plan->price, $planPriceSource, $userCurrency);


            $curlData = [
                'CustomerName'       => $user->name ?: 'Customer',
                'InvoiceValue'       => round($effectivePrice, 2),
                'DisplayCurrencyIso' => $userCurrency,
                'CustomerEmail'      => $user->email ?: 'test@test.com',
                'CallBackUrl'        => route('myfatoorah.process'),
                'ErrorUrl'           => route('myfatoorah.process'),
                'Language'           => app()->getLocale(),
                'CustomerReference'  => "P{$plan->id}U{$user->id}",
                'SourceInfo'         => config('app.name') . ' ' . app()::VERSION . ' - MyFatoorah Integration',
            ];

            $payment = $this->mfService->createInvoice($curlData);

            return redirect()->route('myfatoorah.gateway', ['url' => $payment['invoiceURL']]);
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * Managed Gateway Redirect (Security Whitelisted)
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function gateway(Request $request)
    {
        $url = $request->get('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return abort(404);
        }

        // Security Whitelist: Only allow redirects to MyFatoorah domains
        $parsedUrl = parse_url($url);
        $host = strtolower($parsedUrl['host'] ?? '');
        $allowedHosts = [
            'demo.myfatoorah.com',
            'portal.myfatoorah.com',
        ];

        if (!in_array($host, $allowedHosts)) {
            Log::warning("Blocked unauthorized payment redirect attempt to: {$host}");
            return abort(403, 'Unauthorized redirect target.');
        }

        return redirect()->away($url);
    }




    /**
     * Intermediate loading page
     *
     * @return View
     */
    public function process()
    {
        $paymentId = request('paymentId');
        if (!$paymentId) {
            return abort(404);
        }

        $callbackURL = route('myfatoorah.callback');
        return view('myfatoorah.process', compact('paymentId', 'callbackURL'));
    }

    /**
     * Handle payment callback (Frontend redirect)
     *
     * @return RedirectResponse
     */
    public function callback()
    {
        $paymentId = request('paymentId');
        if (!$paymentId) {
            return abort(404);
        }

        try {
            $data = (object)$this->mfService->getPaymentStatus($paymentId);
            $reference = $data->CustomerReference; // Formatted as P{planId}U{userId} or WT-{wtId}
            
            // Default fallbacks from gateway
            $amount = $data->InvoiceValue;
            $currency = $data->DisplayCurrencyIso ?? 'SAR';

            // Try to get original plan details for better display
            if (str_starts_with($reference, 'P')) {
                preg_match('/P(\d+)U(\d+)/', $reference, $matches);
                if (count($matches) >= 3) {
                    $planId = $matches[1];
                    $plan = \App\Models\MemberPlan::with('country')->find($planId);
                    if ($plan) {
                        // We use the same conversion logic as OnlinePaymentStrategy to show consistent "EGP" vs "SAR"
                        $planPriceSource = config('settings.currency.base', 'USD');
                        
                        // Use "EGP" for display as we forced it in OnlinePaymentStrategy
                        $displayCurrency = 'EGP'; 
                        $currencyService = app(\App\Services\CurrencyService::class);
                        $amount = $currencyService->convert($plan->price, $planPriceSource, $displayCurrency);
                        $currency = $displayCurrency;
                        
                        Log::info("Success Page Override (Plan):", [
                            'original_amount' => $data->InvoiceValue ?? 0,
                            'original_currency' => $data->DisplayCurrencyIso ?? $data->Currency ?? 'SAR',
                            'display_amount' => $amount,
                            'display_currency' => $currency
                        ]);
                    }
                }
            } elseif (str_starts_with($reference, 'WT-')) {
                $transactionId = str_replace('WT-', '', $reference);
                $transaction = \App\Models\WalletTransaction::find($transactionId);
                if ($transaction && !empty($transaction->metadata)) {
                    $metadata = $transaction->metadata;
                    $amount = $metadata['display_amount'] ?? $amount;
                    $currency = $metadata['display_currency'] ?? 'EGP';
                    
                    Log::info("Success Page Override (Wallet):", [
                        'original_amount' => $data->InvoiceValue ?? 0,
                        'original_currency' => $data->DisplayCurrencyIso ?? $data->Currency ?? 'SAR',
                        'display_amount' => $amount,
                        'display_currency' => $currency
                    ]);
                }
            }

            if ($data->InvoiceStatus == 'Paid') {
                $this->callbackHandler->handleSuccess($reference, $paymentId, $data->InvoiceValue);
                
                return redirect()->route('myfatoorah.success', [
                    'paymentId' => $paymentId,
                    'Id'        => $paymentId,
                    'status'    => 'success',
                    'amount'    => round($amount, 2),
                    'currency'  => $currency
                ]);
            } else {
                $error = $data->InvoiceError ?? __('message.payment_failed_desc');
                $this->callbackHandler->handleFailure($reference, $error);
                
                return redirect()->route('myfatoorah.error', ['message' => $error]);
            }
        } catch (Exception $ex) {
            Log::error("MyFatoorah Callback Error: " . $ex->getMessage());
            return redirect()->route('myfatoorah.error', ['message' => $ex->getMessage()]);
        }
    }

    /**
     * Display Success Page
     */
    public function success(Request $request): View
    {
        $status = 'success';
        $payment_id = $request->query('paymentId') ?? $request->query('Id');
        $amount = $request->query('amount');
        $currency = $request->query('currency') ?? 'SAR';
        $date = now()->format('Y/m/d H:i');

        return view('myfatoorah.success', compact('status', 'payment_id', 'amount', 'currency', 'date'));
    }

    /**
     * Display Error Page
     */
    public function error(Request $request): View
    {
        $message = $request->get('message') ?? __('message.generic_payment_error');
        return view('myfatoorah.error', compact('message'));
    }




    /**
     * Handle MyFatoorah Webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request)
    {
        // Validation now handled by VerifyMyFatoorahWebhook middleware
        $input = $request->all();

        if (empty($input['Data']) || empty($input['EventType'])) {
            return $this->errorResponse('Invalid webhook data');
        }


        // EventType 1: Transaction Status Changed
        if ($input['EventType'] == 1) {
            return $this->changeTransactionStatus($input['Data']);
        }

        return $this->successResponse(null, 'Event received');
    }


    /**
     * Update transaction status from webhook data
     *
     * @param array $inputData
     * @return JsonResponse
     */
    private function changeTransactionStatus($inputData)
    {
        try {
            if ($inputData['TransactionStatus'] == 'SUCCESS') {
                $this->callbackHandler->handleSuccess(
                    $inputData['CustomerReference'],
                    $inputData['InvoiceId'],
                    $inputData['InvoiceValue']
                );
            } else {
                $this->callbackHandler->handleFailure(
                    $inputData['CustomerReference'],
                    $inputData['TransactionStatus']
                );
            }
            return $this->successResponse(null, 'Webhook processed successfully');
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * Status Polling Endpoint for API/Frontend
     *
     * @param string $subscription_id
     * @return JsonResponse
     */
    public function statusPoll($subscription_id)
    {
        $subscription = \App\Models\Subscription::findOrFail($subscription_id);

        return $this->successResponse([
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'payment_status' => $subscription->payment_status,
            'is_active' => $subscription->isActive(),
        ]);
    }

}
