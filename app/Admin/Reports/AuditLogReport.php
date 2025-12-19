<?php

namespace App\Admin\Reports;

use App\Models\Activity_log;
use App\Models\AuditLog;

class AuditLogReport extends ReportTemplate
{
    protected function collect(): array
    {

        return Activity_log::latest()->limit(100)->get()->toArray();
    }
}
