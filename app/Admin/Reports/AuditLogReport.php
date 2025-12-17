<?php

namespace App\Admin\Reports;

use App\Models\AuditLog;

class AuditLogReport extends ReportTemplate
{
    protected function collect(): array
    {
        return [];
       // return AuditLog::latest()->limit(100)->get()->toArray();
    }
}
