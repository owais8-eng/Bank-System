<?php

namespace App\Domain\Transaction\Roles;

    use App\Domain\Transaction\RoleStrategy;

    class RoleResolve
    {
        public static function resolve(string $role): RoleStrategy
        {
            return match ($role) {
                'admin'    => new AdminRole(),
                'teller'   => new TellerRole(),
                'customer' => new CustomerRole(),
                default    => new CustomerRole(),
            };
        }
    }
