<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Analytics Demo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #eef2ff, #f8fafc 45%, #ecfeff);
            color: #111827;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 28px 20px 40px; }
        .title { margin: 0 0 8px; font-size: 28px; }
        .hint { margin: 0 0 18px; color: #4b5563; }
        .card {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(4px);
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
            animation: fadeInUp .55s ease both;
        }
        .grid { display: grid; gap: 12px; }
        .grid.metrics { grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); }
        .grid.charts { grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); }
        .metric {
            border: 1px solid #dbeafe;
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            border-radius: 12px;
            padding: 12px;
            transform: translateY(6px);
            opacity: 0;
            animation: popIn .5s ease forwards;
        }
        .metric:nth-child(2) { animation-delay: .06s; }
        .metric:nth-child(3) { animation-delay: .12s; }
        .metric:nth-child(4) { animation-delay: .18s; }
        .metric:nth-child(5) { animation-delay: .24s; }
        .label { font-size: 12px; color: #6b7280; margin-bottom: 6px; }
        .value { font-weight: 700; font-size: 20px; color: #0f172a; }
        .chart-wrap { height: 280px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 9px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; white-space: nowrap; }
        th { background: #f8fafc; }
        tbody tr { transition: background-color .2s ease; }
        tbody tr:hover { background: #f1f5f9; }
        .error {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px;
        }
        .muted { color: #6b7280; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Data Warehouse Analytics Demo</h1>
        <p class="hint">Interactive view for <code>dw_transaction_facts</code> and <code>dw_account_daily_snapshots</code>.</p>

        @if ($error)
            <div class="card error">
                <strong>Warehouse is not ready.</strong><br>
                {{ $error }}
                <div class="muted" style="margin-top: 8px;">
                    Run: <code>php artisan migrate</code> then <code>php artisan analytics:warehouse:build --day={{ now()->toDateString() }}</code>
                </div>
            </div>
        @else
            <div class="card">
                <h2>Summary</h2>
                <div class="grid metrics">
                    <div class="metric">
                        <div class="label">Transaction Fact Rows</div>
                        <div class="value">{{ number_format((int) $summary['transaction_fact_rows']) }}</div>
                    </div>
                    <div class="metric">
                        <div class="label">Account Snapshot Rows</div>
                        <div class="value">{{ number_format((int) $summary['snapshot_rows']) }}</div>
                    </div>
                    <div class="metric">
                        <div class="label">Total Amount in Facts</div>
                        <div class="value">{{ number_format((float) $summary['total_fact_amount'], 2) }}</div>
                    </div>
                    <div class="metric">
                        <div class="label">Latest Fact Date</div>
                        <div class="value">{{ $summary['latest_fact_date'] ?? '-' }}</div>
                    </div>
                    <div class="metric">
                        <div class="label">Latest Snapshot Date</div>
                        <div class="value">{{ $summary['latest_snapshot_date'] ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="grid charts">
                <div class="card">
                    <h2>Daily Transaction Amount Trend</h2>
                    <div class="chart-wrap"><canvas id="dailyTrendChart"></canvas></div>
                </div>
                <div class="card">
                    <h2>Transaction Type Distribution</h2>
                    <div class="chart-wrap"><canvas id="typeChart"></canvas></div>
                </div>
                <div class="card">
                    <h2>Account State Distribution</h2>
                    <div class="chart-wrap"><canvas id="stateChart"></canvas></div>
                </div>
            </div>

            <div class="card">
                <h2>Recent Transaction Facts</h2>
                @if ($transactionFacts->isEmpty())
                    <p class="muted">No data yet. Build warehouse first.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Total</th>
                                    <th>Average</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($transactionFacts as $row)
                                <tr>
                                    <td>{{ $row->metric_date }}</td>
                                    <td>{{ $row->transaction_type }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td>{{ number_format((int) $row->transactions_count) }}</td>
                                    <td>{{ number_format((float) $row->total_amount, 2) }}</td>
                                    <td>{{ number_format((float) $row->avg_amount, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card">
                <h2>Recent Account Daily Snapshots</h2>
                @if ($accountSnapshots->isEmpty())
                    <p class="muted">No data yet. Build warehouse first.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account Type</th>
                                    <th>State</th>
                                    <th>Count</th>
                                    <th>Total Balance</th>
                                    <th>Average Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($accountSnapshots as $row)
                                <tr>
                                    <td>{{ $row->metric_date }}</td>
                                    <td>{{ $row->account_type }}</td>
                                    <td>{{ $row->account_state }}</td>
                                    <td>{{ number_format((int) $row->accounts_count) }}</td>
                                    <td>{{ number_format((float) $row->total_balance, 2) }}</td>
                                    <td>{{ number_format((float) $row->avg_balance, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if (! $error)
        <script>
            const chartData = @json($chartData);
            const axisColor = '#64748b';
            const gridColor = 'rgba(148, 163, 184, 0.25)';

            new Chart(document.getElementById('dailyTrendChart'), {
                type: 'line',
                data: {
                    labels: chartData.dailyTrend.labels,
                    datasets: [
                        {
                            label: 'Total Amount',
                            data: chartData.dailyTrend.amounts,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Transactions Count',
                            data: chartData.dailyTrend.counts,
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.12)',
                            fill: false,
                            tension: 0.35,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    scales: {
                        x: { ticks: { color: axisColor }, grid: { color: gridColor } },
                        y: { ticks: { color: axisColor }, grid: { color: gridColor }, beginAtZero: true },
                        y1: { position: 'right', ticks: { color: axisColor }, grid: { drawOnChartArea: false }, beginAtZero: true },
                    }
                }
            });

            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: chartData.typeDistribution.labels,
                    datasets: [{
                        data: chartData.typeDistribution.counts,
                        backgroundColor: ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutBack' },
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            new Chart(document.getElementById('stateChart'), {
                type: 'bar',
                data: {
                    labels: chartData.stateDistribution.labels,
                    datasets: [{
                        label: 'Accounts',
                        data: chartData.stateDistribution.counts,
                        backgroundColor: '#14b8a6',
                        borderColor: '#0f766e',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutCubic' },
                    scales: {
                        x: { ticks: { color: axisColor }, grid: { color: gridColor } },
                        y: { ticks: { color: axisColor }, grid: { color: gridColor }, beginAtZero: true },
                    }
                }
            });
        </script>
    @endif
</body>
</html>
