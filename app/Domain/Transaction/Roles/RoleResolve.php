<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Roles;

use App\Admin\Roles\TellerRole;

class RoleResolve
{
    public static function resolve(string $role)
    {
        return match ($role) {
            'teller' => new TellerRole,
            'manager' => new ManagerRole,
            'admin' => new AdminRole,
            default => new BaseRole,
        };
    }
}
