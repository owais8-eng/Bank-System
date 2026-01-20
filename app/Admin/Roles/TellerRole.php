<?php

declare(strict_types=1);

namespace App\Admin\Roles;

class TellerRole implements RoleStrategy
{
    public function canManagementAccounts(): bool
    {
        return false;
    }

    public function canViewDashboard(): bool
    {
        return false;
    }

    public function canGenerateReports(): bool
    {
        return false;
    }
}
