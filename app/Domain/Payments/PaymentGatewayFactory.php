<?php

declare(strict_types=1);

namespace App\Domain\Payments;

use App\Infrastructure\Adapters\LegacyBankAdapter;
use App\Infrastructure\Adapters\StripeAdapter;
use Exception;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGateway
    {
        return match ($gateway) {
            'legacy' => new LegacyBankAdapter,
            'stripe' => new StripeAdapter,
            default => throw new Exception('Unsupported payment gateway'),
        };
    }
}
