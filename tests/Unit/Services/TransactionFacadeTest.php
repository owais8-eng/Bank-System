<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DepositService;
use App\Services\TransactionApprovalService;
use App\Services\TransactionFacade;
use App\Services\TransferService;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TransactionFacadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_facade_processes_deposit(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000.00,
        ]);

        $transaction = Transaction::factory()->make();
        $depositService = Mockery::mock(DepositService::class);
        $depositService->shouldReceive('deposit')
            ->once()
            ->with($account, 500.00, null)
            ->andReturn($transaction);

        $facade = new TransactionFacade(
            $depositService,
            Mockery::mock(WithdrawalService::class),
            Mockery::mock(TransferService::class),
            Mockery::mock(TransactionApprovalService::class)
        );

        $result = $facade->processDeposit($account, 500.00);
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_facade_processes_withdrawal(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000.00,
        ]);

        $transaction = Transaction::factory()->make();
        $withdrawalService = Mockery::mock(WithdrawalService::class);
        $withdrawalService->shouldReceive('withdraw')
            ->once()
            ->with($account, 300.00, null)
            ->andReturn($transaction);

        $facade = new TransactionFacade(
            Mockery::mock(DepositService::class),
            $withdrawalService,
            Mockery::mock(TransferService::class),
            Mockery::mock(TransactionApprovalService::class)
        );

        $result = $facade->processWithdrawal($account, 300.00);
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_facade_processes_transfer(): void
    {
        $user = User::factory()->create();
        $fromAccount = Account::factory()->create(['user_id' => $user->id]);
        $toAccount = Account::factory()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->make();
        $transferService = Mockery::mock(TransferService::class);
        $transferService->shouldReceive('transfer')
            ->once()
            ->with($fromAccount, $toAccount, 200.00, null)
            ->andReturn($transaction);

        $facade = new TransactionFacade(
            Mockery::mock(DepositService::class),
            Mockery::mock(WithdrawalService::class),
            $transferService,
            Mockery::mock(TransactionApprovalService::class)
        );

        $result = $facade->processTransfer($fromAccount, $toAccount, 200.00);
        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function test_facade_processes_batch_transactions(): void
    {
        $user = User::factory()->create();
        $account1 = Account::factory()->create(['user_id' => $user->id]);
        $account2 = Account::factory()->create(['user_id' => $user->id]);

        $depositService = Mockery::mock(DepositService::class);
        $depositService->shouldReceive('deposit')
            ->andReturn(Transaction::factory()->make());

        $facade = new TransactionFacade(
            $depositService,
            Mockery::mock(WithdrawalService::class),
            Mockery::mock(TransferService::class),
            Mockery::mock(TransactionApprovalService::class)
        );

        $transactions = [
            ['type' => 'deposit', 'account' => $account1, 'amount' => 100.00],
            ['type' => 'deposit', 'account' => $account2, 'amount' => 200.00],
        ];

        $results = $facade->processBatch($transactions);

        $this->assertCount(2, $results);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
