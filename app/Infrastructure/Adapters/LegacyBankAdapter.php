<?php

namespace App\Infrastructure\Adapters;

use App\Domain\Payments\PaymentGateway;
use App\Infrastructure\Legacy\LegacyBankApi;
use App\Models\Transaction;

class LegacyBankAdapter implements PaymentGateway
{
    private LegacyBankApi $api;

    public function __construct()
    {
        $this->api = new LegacyBankApi();
    }

    public function process(Transaction $transaction): bool
    {
        $response = $this->api->makePayment([
            'amount' => $transaction->amount,
            'account' => $transaction->account_id,
        ]);

        return $response === 'OK';
    }
}
