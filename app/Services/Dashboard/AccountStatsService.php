<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Models\Account;

class AccountStatsService
{
    public function total(): int
    {
        return Account::count();
    }

    public function active(): int
    {
        return Account::where('state', 'active')->count();
    }

    public function closed(): int
    {
        return Account::where('state', 'closed')->count();
    }

    public function suspended(): int
    {
        return Account::where('state', 'suspended')->count();
    }

    public function frozen(): int
    {
        return Account::where('state', 'frozen')->count();
    }

    public function totalBalance(): float
    {
        return (float) Account::sum('balance');
    }
}
