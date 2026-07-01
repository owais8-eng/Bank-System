<?php

declare(strict_types=1);

namespace App\Exceptions;

use DomainException;

class InvalidAccountStateException extends DomainException
{
    // كلاس فارغ يعمل كمعرف مخصص لهذا النوع من الأخطاء
}
