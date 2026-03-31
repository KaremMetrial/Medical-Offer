<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class VerifyMyFatoorahWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. IP Whitelisting (Optional but recommended for high security)
        $allowedIps = config('myfatoorah.allowed_webhook_ips', [
            '94.205.109.214',
            '94.205.109.11',
            '95.216.59.183',
            '52.209.155.101',
            '52.17.20.129',
        ]);

        if (!app()->environment('local') && !in_array($request->ip(), $allowedIps)) {
             Log::warning('MyFatoorah Webhook: Unauthorized IP address: ' . $request->ip());
             return response()->json(['error' => 'Unauthorized IP'], 403);
        }

        // 2. Signature Validation
        $secretKey = config('myfatoorah.webhook_secret_key');
        if (empty($secretKey)) {
            Log::error('MyFatoorah Webhook: Secret key is not configured.');
            return $next($request); // Skip validation if not configured to avoid blocking (standard practice during setup)
        }

        $mfSignature = $request->header('MyFatoorah-Signature');
        if (!$mfSignature) {
            Log::warning('MyFatoorah Webhook: Missing signature header.');
            return response()->json(['error' => 'Missing signature'], 400);
        }

        $content = $request->getContent();
        $expectedSignature = base64_encode(hash_hmac('sha256', $content, $secretKey, true));

        if (!hash_equals($expectedSignature, $mfSignature)) {
            Log::error('MyFatoorah Webhook: Invalid signature match.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return $next($request);
    }
}
