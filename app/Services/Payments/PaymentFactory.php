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
            PaymentMethod::WALLET->value => new WalletPaymentStrategy(),
            PaymentMethod::ONLINE->value => new OnlinePaymentStrategy(),
            default => throw new InvalidArgumentException("Payment method [{$methodValue}] is not supported."),
        };
    }
}
