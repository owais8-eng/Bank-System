<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Accounts\States\{
    AccountState,
    ActiveState,
    FrozenState,
    SuspendedState,
    ClosedState
};
use App\Models\Account;

class AccountServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(AccountState::class, function ($app, $params) {
            /** @var Account $account */
            $account = $params['account'];

            return match ($account->state) {
                'active'    => new ActiveState($account),
                'frozen'    => new FrozenState(),
                'suspended' => new SuspendedState($account),
                'closed'    => new ClosedState(),
                default     => throw new \Exception('Invalid state'),
            };
        });
    }
}
