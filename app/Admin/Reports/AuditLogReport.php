<?php

declare(strict_types=1);

namespace App\Admin\Reports;

use App\Models\ACtivityLog;

class AuditLogReport extends ReportTemplate
{
    protected function collect(): array
    {

        return ActivityLog::latest()->limit(100)->get()->toArray();
    }
}
