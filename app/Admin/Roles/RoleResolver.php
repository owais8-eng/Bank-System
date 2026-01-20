<?php

declare(strict_types=1);

namespace App\Admin\Roles;

class RoleResolver
{
    public static function resolve(string $role): RoleStrategy
    {
        return match ($role) {
            'admin' => new AdminRole,
            'manager' => new ManagerRole,
            'teller' => new TellerRole,
            'customer' => new CustomerRole,
            default => new CustomerRole,
        };
    }
}
