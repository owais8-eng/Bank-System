<?php

namespace App\Http\Controllers;

use App\Console\Commands\ProcessRecurringTransactions;
use App\Http\Requests\TransferRequest;
use App\Models\Account;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

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

    public function transfer(TransferRequest $request, TransactionService $service)
    {
        $from = Account::findOrFail($request->from_account_id);
        $to   = Account::findOrFail($request->to_account_id);

        $transaction = $service->transfer(
            $from,
            $to,
            $request->amount,
            $request->description
        );

        $transaction = $transaction->fresh();

        return response()->json([
            'success' => true,
            'data' => [
                'id'            => $transaction->id,
                'from_account'  => $transaction->account_id,
                'to_account'    => $transaction->to_account_id,
                'amount'        => $transaction->amount,
                'status'        => $transaction->status,
                'approved_type' => $transaction->approved_type,
                'description'   => $transaction->description,
                'created_at'    => $transaction->created_at,
            ]
        ]);
    }

    public function scheduleTransaction(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'frequency'       => 'required|in:daily,weekly,monthly,yearly',
            'next_run_at'        => 'required|date',
        ]);

        $scheduled = RecurringTransaction::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $scheduled
        ]);
    }

    public function transactionHistory(Account $account)
{
    $transactions = Transaction::where('account_id', $account->id)
        ->orWhere('to_account_id', $account->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $transactions
    ]);
}
public function auditLog(Account $account)
{
    $logs = Activity::where('subject_type', Transaction::class)
        ->where('subject_id', $account->id)
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'data' => $logs
    ]);
}

}
