<?php

namespace App\Infrastructure\Adapters;

use App\Domain\Payments\PaymentGateway;
use App\Models\Transaction;

class StripeAdapter implements PaymentGateway
{
    public function process(Transaction $transaction): bool
    {
        // محاكاة Stripe
        return true;
    }
}
