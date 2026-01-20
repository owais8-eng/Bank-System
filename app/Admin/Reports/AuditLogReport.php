<?php

declare(strict_types=1);

namespace App\Admin\Reports;

use App\Models\Activity_log;

class AuditLogReport extends ReportTemplate
{
    protected function collect(): array
    {

        return Activity_log::latest()->limit(100)->get()->toArray();
    }
}
