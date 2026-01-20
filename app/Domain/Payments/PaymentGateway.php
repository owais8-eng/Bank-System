<?php

declare(strict_types=1);

namespace App\Domain\Payments;

use App\Models\Transaction;

interface PaymentGateway
{
    public function process(Transaction $transaction): bool;
}
