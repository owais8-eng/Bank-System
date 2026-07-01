<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Throwable;

class WarehouseDemoController extends Controller
{
    public function index()
    {
        try {
            $summary = [
                'transaction_fact_rows' => DB::table('dw_transaction_facts')->count(),
                'snapshot_rows' => DB::table('dw_account_daily_snapshots')->count(),
                'total_fact_amount' => (float) DB::table('dw_transaction_facts')->sum('total_amount'),
                'latest_fact_date' => DB::table('dw_transaction_facts')->max('metric_date'),
                'latest_snapshot_date' => DB::table('dw_account_daily_snapshots')->max('metric_date'),
            ];

            $transactionFacts = DB::table('dw_transaction_facts')
                ->orderByDesc('metric_date')
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            $accountSnapshots = DB::table('dw_account_daily_snapshots')
                ->orderByDesc('metric_date')
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            $dailyTrendRows = DB::table('dw_transaction_facts')
                ->select('metric_date')
                ->selectRaw('SUM(total_amount) as total_amount')
                ->selectRaw('SUM(transactions_count) as transactions_count')
                ->groupBy('metric_date')
                ->orderBy('metric_date')
                ->limit(30)
                ->get();

            $typeDistributionRows = DB::table('dw_transaction_facts')
                ->select('transaction_type')
                ->selectRaw('SUM(transactions_count) as total_count')
                ->groupBy('transaction_type')
                ->orderByDesc('total_count')
                ->get();

            $stateDistributionRows = DB::table('dw_account_daily_snapshots')
                ->select('account_state')
                ->selectRaw('SUM(accounts_count) as total_count')
                ->groupBy('account_state')
                ->orderByDesc('total_count')
                ->get();

            $chartData = [
                'dailyTrend' => [
                    'labels' => $dailyTrendRows->pluck('metric_date')->values(),
                    'amounts' => $dailyTrendRows->pluck('total_amount')->map(fn ($value) => (float) $value)->values(),
                    'counts' => $dailyTrendRows->pluck('transactions_count')->map(fn ($value) => (int) $value)->values(),
                ],
                'typeDistribution' => [
                    'labels' => $typeDistributionRows->pluck('transaction_type')->values(),
                    'counts' => $typeDistributionRows->pluck('total_count')->map(fn ($value) => (int) $value)->values(),
                ],
                'stateDistribution' => [
                    'labels' => $stateDistributionRows->pluck('account_state')->values(),
                    'counts' => $stateDistributionRows->pluck('total_count')->map(fn ($value) => (int) $value)->values(),
                ],
            ];

            $error = null;
        } catch (Throwable $exception) {
            $summary = null;
            $transactionFacts = collect();
            $accountSnapshots = collect();
            $chartData = [
                'dailyTrend' => ['labels' => [], 'amounts' => [], 'counts' => []],
                'typeDistribution' => ['labels' => [], 'counts' => []],
                'stateDistribution' => ['labels' => [], 'counts' => []],
            ];
            $error = $exception->getMessage();
        }

        return view('warehouse-demo', compact(
            'summary',
            'transactionFacts',
            'accountSnapshots',
            'chartData',
            'error'
        ));
    }
}
