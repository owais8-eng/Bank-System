<?php

declare(strict_types=1);

namespace App\Admin\Reports;

abstract class ReportTemplate
{
    final public function generate(): array
    {

        return $this->collect();
    }

    abstract protected function collect(): array;
}
