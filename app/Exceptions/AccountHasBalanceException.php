<?php

declare(strict_types=1);

namespace App\Exceptions;

use DomainException;

class AccountHasBalanceException extends DomainException
{
    // يمكنك ترك هذا الكلاس فارغاً، فهو يعمل فقط كـ "هوية" للخطأ
}
