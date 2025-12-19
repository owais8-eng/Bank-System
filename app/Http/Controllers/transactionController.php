<?php

namespace App\Http\Controllers;

use App\Domain\Accounts\Decorator\AccountAuthorizationFactory;
use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class transactionController extends Controller
{
    private TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }
    public function deposit(Request $request, Account $account)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        return response()->json(
            $this->service->deposit($account, $data['amount'], $data['description'] ?? null)
        );
    }

    public function withdraw(Request $request, Account $account)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);


        return response()->json(
            $this->service->withdraw($account, $data['amount'], $data['description'] ?? null)
        );
    }

    public function transfer(Request $request, Account $from)
    {
        $data = $request->validate([
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        $to = Account::findOrFail($data['to_account_id']);

        return response()->json(
            $this->service->transfer($from, $to, $data['amount'], $data['description'] ?? null)
        );
    }


}
