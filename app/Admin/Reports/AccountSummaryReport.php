<?php

namespace App\Admin\Reports;

use App\Models\Account;

class AccountSummaryReport extends ReportTemplate
{
    protected function collect(): array
    {
        return Account::select('type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('type')
            ->get()
            ->toArray();
    }
}
