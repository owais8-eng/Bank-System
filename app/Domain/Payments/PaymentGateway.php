<?php

namespace App\Domain\Payments;

use App\Models\Transaction;

interface PaymentGateway
{
    public function process(Transaction $transaction): bool;
}
