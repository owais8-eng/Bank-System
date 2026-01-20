<?php

declare(strict_types=1);

namespace App\Admin\Reports;

use Exception;

class ReportFactory
{
    public static function make(string $type): ReportTemplate
    {
        return match ($type) {
            'daily_transactions' => new DailyTransactionsReport,
            'account_summary' => new AccountSummaryReport,
            'audit_log' => new AuditLogReport,
            default => throw new Exception('Invalid report type'),
        };
    }
}
