<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Admin\Roles\RoleResolver;
use App\Http\Requests\Accountrequest;
use App\Models\Account;
use App\Services\AccountCompositeService;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private AccountService $service) {}

    public function index()
    {
        return response()->json(Account::with('children')->get());
    }

    public function show(Account $account)
    {
        return response()->json(
            $account->load('children')
        );
    }

    public function store(Accountrequest $request)
    {

        $role = RoleResolver::resolve(auth()->user()->role);
        if (! $role->canManagementAccounts()) {
            return response()->json([
                'message' => 'Unauthorized to create accounts',
            ], 403);
        }
        $validated = $request->validated();

        $account = $this->service->create($validated);

        return response()->json($account, 201);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'nickname' => 'nullable|string',
            'daily_limit' => 'nullable|numeric|min:0',
        ]);

        $account = $this->service->updateAccount($account, $validated);

        return response()->json($account);
    }

    public function changeState(Request $request, Account $account)
    {
        $validated = $request->validate([
            'state' => 'required|in:active,frozen,suspended,closed',
        ]);

        $this->service->changeState($account, $validated['state']);

        return response()->json([
            'message' => 'Account state updated successfully',
        ]);
    }

    public function close(Account $account)
    {
        $this->service->closeAccount($account);

        return response()->json([
            'message' => 'Account closed successfully',
        ]);
    }

    public function getTotalBalance(Account $account)
    {
        $compositeService = app(AccountCompositeService::class);
        $totalBalance = $compositeService->getTotalBalance($account);

        return response()->json([
            'account_id' => $account->id,
            'account_type' => $account->type,
            'individual_balance' => $account->balance,
            'total_hierarchy_balance' => $totalBalance,
            'has_children' => $account->children()->count() > 0,
            'children_count' => $account->children()->count(),
        ]);
    }

    public function getAccountHierarchy(Account $account)
    {
        $compositeService = app(AccountCompositeService::class);
        $allAccounts = $compositeService->getAllAccountsInHierarchy($account);

        return response()->json([
            'parent_account' => [
                'id' => $account->id,
                'type' => $account->type,
                'balance' => $account->balance,
                'state' => $account->state,
                'nickname' => $account->nickname,
            ],
            'hierarchy_accounts' => collect($allAccounts)->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'type' => $acc->type,
                    'balance' => $acc->balance,
                    'state' => $acc->state,
                    'nickname' => $acc->nickname,
                    'parent_id' => $acc->parent_id,
                ];
            }),
            'total_accounts' => count($allAccounts),
            'total_balance' => collect($allAccounts)->sum('balance'),
        ]);
    }

    public function checkTransactionAbility(Request $request, Account $account)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = (float) $request->amount;
        $compositeService = app(AccountCompositeService::class);
        $canTransact = $compositeService->canPerformTransaction($account, $amount);

        return response()->json([
            'account_id' => $account->id,
            'amount' => $amount,
            'can_perform_transaction' => $canTransact,
            'hierarchy_checked' => $account->children()->count() > 0,
            'children_count' => $account->children()->count(),
            'individual_balance' => $account->balance,
            'daily_limit' => $compositeService->getGroupDailyLimit($account),
        ]);
    }

    public function getGroupStatistics(Account $account)
    {
        $compositeService = app(AccountCompositeService::class);

        $children = $account->children()->get();
        $totalBalance = $compositeService->getTotalBalance($account);
        $dailyLimit = $compositeService->getGroupDailyLimit($account);

        $accountTypes = $children->pluck('type')->unique()->values();
        $activeAccounts = $children->where('state', 'active')->count();
        $totalAccounts = $children->count() + 1;

        return response()->json([
            'parent_account' => [
                'id' => $account->id,
                'type' => $account->type,
                'balance' => $account->balance,
                'state' => $account->state,
            ],
            'group_statistics' => [
                'total_accounts' => $totalAccounts,
                'active_accounts' => $activeAccounts,
                'account_types' => $accountTypes,
                'total_balance' => $totalBalance,
                'daily_limit' => $dailyLimit,
                'has_hierarchy' => $children->count() > 0,
            ],
            'children_summary' => $children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'type' => $child->type,
                    'balance' => $child->balance,
                    'state' => $child->state,
                    'nickname' => $child->nickname,
                ];
            }),
        ]);
    }
}
