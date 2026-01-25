<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters;

use App\Domain\Payments\PaymentGateway;
use App\Models\Transaction;

class StripeAdapter implements PaymentGateway
{
    public function process(Transaction $transaction): bool
    {

        return true;
    }
}
