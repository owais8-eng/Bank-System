<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'frequency'       => 'required|in:daily,weekly,monthly',
            'next_run_at'     => 'required|date'
        ]);

        return response()->json(
            RecurringTransaction::create($data),
            201
        );
    }
}
