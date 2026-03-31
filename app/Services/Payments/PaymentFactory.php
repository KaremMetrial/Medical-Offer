<?php

namespace App\Services\Payments;

use App\Enums\PaymentMethod;
use InvalidArgumentException;

class PaymentFactory
{
    /**
     * @param string|PaymentMethod $method
     * @return PaymentStrategyInterface
     */
    public static function make($method): PaymentStrategyInterface
    {
        $methodValue = $method instanceof PaymentMethod ? $method->value : $method;

        return match ($methodValue) {
            PaymentMethod::WALLET->value => app(WalletPaymentStrategy::class),
            PaymentMethod::ONLINE->value => app(OnlinePaymentStrategy::class),
            default => throw new InvalidArgumentException("Payment method [{$methodValue}] is not supported."),
        };

    }
}
