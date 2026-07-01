<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DataWarehouseTransformService
{
    public function buildForDateRange(Carbon $from, Carbon $to): void
    {
        $period = CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay());

        foreach ($period as $day) {
            $date = Carbon::instance($day);
            $this->buildTransactionFactsForDay($date);
            $this->buildAccountSnapshotsForDay($date);
        }
    }

    public function truncateWarehouse(): void
    {
        DB::table('dw_transaction_facts')->truncate();
        DB::table('dw_account_daily_snapshots')->truncate();
    }

    private function buildTransactionFactsForDay(Carbon $date): void
    {
        $rows = DB::table('transactions')
            ->selectRaw('DATE(created_at) as metric_date')
            ->selectRaw('type as transaction_type')
            ->selectRaw('status')
            ->selectRaw('account_id')
            ->selectRaw('user_id')
            ->selectRaw('COUNT(*) as transactions_count')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->selectRaw('COALESCE(AVG(amount), 0) as avg_amount')
            ->selectRaw('COALESCE(MIN(amount), 0) as min_amount')
            ->selectRaw('COALESCE(MAX(amount), 0) as max_amount')
            ->whereDate('created_at', $date->toDateString())
            ->groupByRaw('DATE(created_at), type, status, account_id, user_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('dw_transaction_facts')->updateOrInsert(
                [
                    'metric_date' => $row->metric_date,
                    'transaction_type' => $row->transaction_type,
                    'status' => $row->status,
                    'account_id' => $row->account_id,
                    'user_id' => $row->user_id,
                ],
                [
                    'transactions_count' => (int) $row->transactions_count,
                    'total_amount' => (float) $row->total_amount,
                    'avg_amount' => (float) $row->avg_amount,
                    'min_amount' => (float) $row->min_amount,
                    'max_amount' => (float) $row->max_amount,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function buildAccountSnapshotsForDay(Carbon $date): void
    {
        $rows = DB::table('accounts')
            ->selectRaw('? as metric_date', [$date->toDateString()])
            ->selectRaw('type as account_type')
            ->selectRaw('state as account_state')
            ->selectRaw('COUNT(*) as accounts_count')
            ->selectRaw('COALESCE(SUM(balance), 0) as total_balance')
            ->selectRaw('COALESCE(AVG(balance), 0) as avg_balance')
            ->selectRaw('COALESCE(MIN(balance), 0) as min_balance')
            ->selectRaw('COALESCE(MAX(balance), 0) as max_balance')
            ->groupBy('type', 'state')
            ->get();

        foreach ($rows as $row) {
            DB::table('dw_account_daily_snapshots')->updateOrInsert(
                [
                    'metric_date' => $row->metric_date,
                    'account_type' => $row->account_type,
                    'account_state' => $row->account_state,
                ],
                [
                    'accounts_count' => (int) $row->accounts_count,
                    'total_balance' => (float) $row->total_balance,
                    'avg_balance' => (float) $row->avg_balance,
                    'min_balance' => (float) $row->min_balance,
                    'max_balance' => (float) $row->max_balance,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
