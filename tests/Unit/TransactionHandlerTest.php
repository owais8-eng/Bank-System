<?php

namespace Tests\Unit;

use App\Domain\Transaction\AutoApprovalHandler;
use App\Models\Transaction;
use Tests\TestCase;

class TransactionHandlerTest extends TestCase
{
 public function testAutoApprovalHandler()
    {
$transaction = Transaction::factory()->create(['amount' => 500]);

        $handler = new AutoApprovalHandler();
        $handler->handle($transaction);

        $this->assertEquals('approved', $transaction->status);
    }
}
