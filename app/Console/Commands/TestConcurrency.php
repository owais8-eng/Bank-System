<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class TestConcurrency extends Command
{
    protected $signature = 'test:concurrency';

    protected $description = 'إرسال 100 طلب متزامن لاختبار الأداء';

    public function handle()
    {
        $this->info('بدء إرسال 100 عملية إيداع متزامنة...');

        $responses = Http::pool(fn (Pool $pool) => [
            collect(range(1, 100))->map(function ($i) use ($pool) {
                return $pool->as((string) $i)->post('http://127.0.0.1:8000/api/accounts/1/deposit', [
                    'amount' => 10,
                    'description' => "Test Transaction $i",
                ]);
            }),
        ]);

        $this->info('تم الانتهاء! اذهب الآن إلى Laravel Telescope.');
    }
}
