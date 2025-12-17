<?php


namespace App\Admin\Reports;

abstract class ReportTemplate
{

    final public function generate() : array {

        return $this->collect();
    }

    abstract protected function collect(): array;
}
