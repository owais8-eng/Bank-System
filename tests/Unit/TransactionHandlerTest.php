<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Transaction\AutoApprovalHandler;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_approval_handler(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'amount' => 500,
            'status' => 'pending',
        ]);

        $handler = new AutoApprovalHandler();
        $handler->handle($transaction);

        $transaction->refresh();
        $this->assertEquals('approved', $transaction->status);
        $this->assertEquals('auto', $transaction->approved_type);
    }
}
