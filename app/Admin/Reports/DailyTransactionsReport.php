<?php

declare(strict_types=1);

namespace App\Admin\Reports;

use App\Models\Transaction;

class DailyTransactionsReport extends ReportTemplate
{
    protected function collect(): array
    {
        return Transaction::whereDate('created_at', today())
            ->get()
            ->toArray();
    }
}
