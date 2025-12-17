<?php

namespace App\Admin\Roles;

interface RoleStrategy
{
    public function canManagementAccounts(): bool;
    public function canViewDashboard(): bool;
    public function canGenerateReports(): bool;
    
}
