<?php

declare(strict_types=1);

namespace App\Domain\Accounts;

use App\Domain\Accounts\States\AccountState as StatesAccountState;
use App\Domain\Accounts\States\ActiveState;
use App\Domain\Accounts\States\ClosedState;
use App\Domain\Accounts\States\FrozenState;
use App\Domain\Accounts\States\SuspendedState;

class StateResolver
{
    public static function resolve(string $state): StatesAccountState
    {
        return match ($state) {
            'active' => new ActiveState,
            'frozen' => new FrozenState,
            'suspended' => new SuspendedState,
            'closed' => new ClosedState,
            default => throw new \InvalidArgumentException("Invalid account state: {$state}"),
        };
    }
}
