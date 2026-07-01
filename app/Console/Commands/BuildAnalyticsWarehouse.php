<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Analytics\DataWarehouseTransformService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use InvalidArgumentException;

class BuildAnalyticsWarehouse extends Command
{
    protected $signature = 'analytics:warehouse:build
        {--from= : Start date (Y-m-d)}
        {--to= : End date (Y-m-d)}
        {--day= : Single date (Y-m-d)}
        {--truncate : Truncate warehouse tables before build}';

    protected $description = 'Build/refresh analytics warehouse tables using transform operations';

    public function handle(DataWarehouseTransformService $service): int
    {
        try {
            [$from, $to] = $this->resolveDateRange();
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if ((bool) $this->option('truncate')) {
            $service->truncateWarehouse();
            $this->info('Warehouse tables truncated.');
        }

        $service->buildForDateRange($from, $to);

        $this->info(sprintf(
            'Warehouse build completed for range %s -> %s',
            $from->toDateString(),
            $to->toDateString()
        ));

        return self::SUCCESS;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveDateRange(): array
    {
        $day = $this->option('day');
        $from = $this->option('from');
        $to = $this->option('to');

        if (is_string($day) && $day !== '') {
            $date = Carbon::createFromFormat('Y-m-d', $day);

            return [$date, $date];
        }

        if (! is_string($from) || $from === '' || ! is_string($to) || $to === '') {
            $today = Carbon::today();

            return [$today, $today];
        }

        $fromDate = Carbon::createFromFormat('Y-m-d', $from);
        $toDate = Carbon::createFromFormat('Y-m-d', $to);

        if ($fromDate->greaterThan($toDate)) {
            throw new InvalidArgumentException('Invalid range: --from must be before or equal to --to.');
        }

        return [$fromDate, $toDate];
    }
}
