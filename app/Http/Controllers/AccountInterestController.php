<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountInterestService;
use Illuminate\Http\Request;

class AccountInterestController extends Controller
{
    public function calculate(Account $account,AccountInterestService $service)
    {
        $interest = $service->calculateInterest($account);

        return response()->json([
             'account_id' => $account->id,
            'account_type' => $account->type,
            'balance' => $account->balance,
            'interest' => $interest,
        ]);

    }
}
