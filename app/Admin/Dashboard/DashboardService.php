<?php

declare(strict_types=1);

namespace App\Admin\Dashboard;

class DashboardService
{
    public function __construct(private AdminDashboardFacade $facade) {}

    public function getOverview(): array
    {
        return $this->facade->overview();
    }
}
