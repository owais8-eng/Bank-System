<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

use App\Models\Account as AccountModel;

class AccountAdapter implements Account
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    public function getBalance(): float
    {
        return (float) $this->model->balance;
    }

    public function withdraw(float $amount): bool
    {
        if ($amount > $this->getBalance()) {
            return false;
        }

        return true;
    }

    public function getDescription(): string
    {
        return "Account: {$this->model->name}";
    }

    public function getModel(): AccountModel
    {
        return $this->model;
    }
}
