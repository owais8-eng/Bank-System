<?php

namespace App\Admin\Roles;

class AdminRole implements RoleStrategy
{
    public function canManagementAccounts(): bool
    {
        return true;
    }
    public function canViewDashboard(): bool
    {
        return true;
    }

    public function canGenerateReports(): bool
    {
        return true;
    }
}
