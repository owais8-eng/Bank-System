<?php

declare(strict_types=1);

namespace App\Admin\Dashboard;

use App\Services\Dashboard\AccountStatsService;
use App\Services\Dashboard\TransactionStatsService;

class AdminDashboardFacade
{
    public function __construct(
        private AccountStatsService $accountStats,
        private TransactionStatsService $transactionStats
    ) {}

    public function overview(): array
    {
        return [
            'total_accounts' => $this->accountStats->total(),
            'active_accounts' => $this->accountStats->active(),
            'closed_accounts' => $this->accountStats->closed(),
            'suspended_accounts' => $this->accountStats->suspended(),
            'frozen_accounts' => $this->accountStats->frozen(),
            'total_balance' => $this->accountStats->totalBalance(),
            'transactions_today' => $this->transactionStats->todayCount(),
        ];
    }
}
