<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Admin\Roles\RoleResolver;
use App\Http\Requests\Accountrequest;
use App\Models\Account;
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
}
