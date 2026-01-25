<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Accounts\States\AccountState;
use App\Domain\Accounts\States\ActiveState;
use App\Domain\Accounts\States\ClosedState;
use App\Domain\Accounts\States\FrozenState;
use App\Domain\Accounts\States\SuspendedState;
use App\Models\Account;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AccountState::class, function ($app, $params) {
            /** @var Account $account */
            $account = $params['account'];

            return match ($account->state) {
                'active' => new ActiveState,
                'frozen' => new FrozenState,
                'suspended' => new SuspendedState,
                'closed' => new ClosedState,
                default => throw new \Exception('Invalid state'),
            };
        });
    }
}
