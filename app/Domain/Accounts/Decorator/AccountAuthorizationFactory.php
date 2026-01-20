<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

use App\Models\Account;

class AccountAuthorizationFactory
{
    public function make(Account $account): AccountAuthorization
    {
        // السلوك الأساسي
        $authorization = new BaseAccountAuthorization($account);

        // حساب جاري: يسمح بالسحب الزائد
        if ($account->type === 'checking') {
            $authorization = new OverdraftProtectionDecorator(
                $authorization,
                10000 // حد السحب الزائد
            );
        }

        // حساب توفير: لا يسمح بالسحب الزائد
        if ($account->type === 'savings') {
            $authorization = new OverdraftProtectionDecorator(
                $authorization,
                0
            );
        }

        // loan و investment
        // لا Decorators (السحب غير منطقي)

        return $authorization;
    }
}
