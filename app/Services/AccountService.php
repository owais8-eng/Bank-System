<?php

namespace App\Services;

use App\Accounts\StateResolver;
use App\Domain\Accounts\Decorator\BaseAccount;
use App\Domain\Accounts\Decorator\OverdraftProtectionDecorator;
use App\Domain\Accounts\Decorator\PremiumAccountDecorator;
use App\Http\Requests\Accountrequest;
use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountService
{

    public function create(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            return Account::create([
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'balance' => $data['initial_balance'] ?? 0,
                'parent_id' => $data['parent_id'] ?? null,
                'state' => 'active',
                'nickname' => $data['nickname'] ?? null,
                'daily_limit' => $data['daily_limit'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);
        });
    }


    public function updateAccount(Account $account, array $data): Account
    {
        if ($account->state === 'closed') {
            throw new \DomainException('Cannot update closed account');
        }

        $account->update($data);

        return $account;
    }

    public function changeState(Account $account, string $state): void
    {
        $account->changeState($state);
    }

    public function closeAccount(Account $account): void
    {
        if ($account->balance != 0) {
            throw new Exception('Account must have zero balance before closure');
        }

        $account->changeState('closed');
    }

    public function deposit(Account $account, float $amount): void
    {
        $state = StateResolver::resolve($account->state);
        $state->deposit($account, $amount);
    }
    public function withdraw(Account $account, float $amount): void
    {
        $state = StateResolver::resolve($account->state);
        $state->withdraw($account, $amount);
    }



}
