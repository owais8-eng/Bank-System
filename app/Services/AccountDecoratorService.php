<?php

namespace App\Services;


use App\Domain\Accounts\Decorator\Account;
use App\Domain\Accounts\Decorator\InsuranceDecorator;
use App\Domain\Accounts\Decorator\OverdraftDecorator;
use App\Domain\Accounts\Decorator\PremiumDecorator;

class AccountDecoratorService
{
    public function applyFeatures($account, array $features)
    {
        foreach ($features as $feature) {
            switch ($feature) {
                case 'overdraft':
                    $account = new OverdraftDecorator($account, 500);
                    break;
                case 'premium':
                    $account = new PremiumDecorator($account);
                    break;
                case 'insurance':
                    $account = new InsuranceDecorator($account);
                    break;
            }
        }

        return $account;
    }
}
