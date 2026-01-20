<?php

declare(strict_types=1);

namespace App\Admin\Roles;

interface RoleStrategy
{
    public function canManagementAccounts(): bool;

    public function canViewDashboard(): bool;

    public function canGenerateReports(): bool;
}
