<?php

namespace App\Admin\Dashboard;

use App\Models\Account;

class AdminDashboardFacade
{
    public function overview(): array
    {
        return [
            'total_accounts'   => Account::count(),
            'active_accounts'  => Account::where('state', 'active')->count(),
            'total_balance'    => Account::sum('balance'),
          //  'transactions_today' =>
          //  Transaction::whereDate('created_at', today())->count(),
        ];
    }
}
