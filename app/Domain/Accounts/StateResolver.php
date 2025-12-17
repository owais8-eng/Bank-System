<?php

namespace App\Domain\Accounts;

use App\Domain\Accounts\States\ActiveState;
use App\Domain\Accounts\States\ClosedState;
use App\Domain\Accounts\States\FrozenState;
use App\Domain\Accounts\States\SuspendedState;
use App\Domain\Accounts\States\AccountState as StatesAccountState;

class StateResolver
{
    public static function resolve(string $state): StatesAccountState
    {
        return match ($state) {
            'active'    => new ActiveState(),
            'frozen'    => new FrozenState(),
            'suspended' => new SuspendedState(),
            'closed'    => new ClosedState(),
        };
    }
}
