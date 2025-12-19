<?php

namespace App\Services\Dashboard;

use App\Models\Transaction;

class TransactionStatsService
{
    public function todayCount(): int
    {
        return Transaction::whereDate('created_at', today())->count();
    }
}
