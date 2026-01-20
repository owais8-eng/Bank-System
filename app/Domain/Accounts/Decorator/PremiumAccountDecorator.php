<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

class PremiumAccountDecorator extends AccountAuthorizationDecorator
{
    public function authorizeWithdraw(float $amount): void
    {
        // يمكن إضافة أي ميزات مستقبلية مثل logging أو إشعارات
        parent::authorizeWithdraw($amount);
    }
}
