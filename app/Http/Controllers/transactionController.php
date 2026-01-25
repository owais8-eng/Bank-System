<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Accounts\Decorator\AccountAdapter;
use App\Http\Requests\TransferRequest;
use App\Models\Account;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Services\AccountDecoratorService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class transactionController extends Controller
{
    private TransactionService $service;
    private AccountDecoratorService $decoratorService;

    public function __construct(TransactionService $service,AccountDecoratorService $decoratorService)
    {
        $this->service = $service;
        $this->decoratorService = $decoratorService;
    }

    public function deposit(Request $request, Account $account)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'gateway' => 'required|string|in:stripe,legacy',
            'features' => 'nullable|array',
            'features.*' => 'string|in:overdraft,premium,insurance',
        ]);

        $amount = (float) $data['amount'];


        $domainAccount = new AccountAdapter($account);


        $decorated = $this->decoratorService->applyFeatures($domainAccount, $data['features'] ?? []);


        $transaction = $this->service->deposit($account, $amount, $data['gateway'], $data['description'] ?? null);

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'description' => $decorated->getDescription(),
            'balance_after' => $decorated->getBalance(),
        ]);
    }

    public function withdraw(Request $request, Account $account)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string|in:overdraft,premium,insurance',
        ]);

        $amount = (float) $data['amount'];

        $domainAccount = new AccountAdapter($account);

        $decorated = $this->decoratorService->applyFeatures(
            $domainAccount,
            $data['features'] ?? []
        );

        if (! $decorated->withdraw($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal denied: insufficient balance'
            ], 422);
        }

        $transaction = $this->service->withdraw(
            $account,
            $amount,
            $data['description'] ?? null
        );

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'description' => $decorated->getDescription(),
            'balance_after' => $decorated->getBalance(),
        ]);
    }

    public function transfer(TransferRequest $request)
    {
        $fromModel = Account::findOrFail($request->from_account_id);
        $toModel   = Account::findOrFail($request->to_account_id);

        $fromDomain = new AccountAdapter($fromModel);
        $toDomain   = new AccountAdapter($toModel);

        $fromDecorated = $this->decoratorService->applyFeatures(
            $fromDomain,
            $request->features ?? []
        );


        if (method_exists($fromDecorated, 'authorizeWithdraw')) {
            $fromDecorated->authorizeWithdraw($request->amount);
        }

        $transaction = $this->service->transfer(
            $fromModel,
            $toModel,
            (float) $request->amount,
            $request->description
        );

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'from_description' => $fromDecorated->getDescription(),
        ]);
    }


    public function scheduleTransaction(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'next_run_at' => 'required|date',
        ]);

        $scheduled = RecurringTransaction::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $scheduled,
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
            'data' => $transactions,
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
            'data' => $logs,
        ]);
    }
}
