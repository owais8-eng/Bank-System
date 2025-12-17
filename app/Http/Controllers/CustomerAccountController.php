<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;

class CustomerAccountController extends Controller
{
    public function index()
    {
        $account = Account::where('user_id',auth()->id())->get();

        return response()->json($account,200);
    }

    public function show(Account $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        return response()->json($account);
    }

    public function update(Request $request, Account $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        return response()->json(
            app(AccountService::class)->updateAccount(
                $account,
                $request->only(['nickname'])
            )
        );
    }

}
