<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->account = Account::factory()->create([
            'balance' => 100,
            'type' => 'checking',
            'user_id' => $this->user->id,
            'nickname' => 'Main Account',
        ]);
    }

    #[Test]
    public function it_can_deposit_money()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/accounts/{$this->account->id}/deposit", [
            'amount' => 50,
            'description' => 'Deposit test',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'transaction' => [
                    'amount' => 50,
                    'type' => 'deposit',
                ],
            ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => 150,
        ]);
    }
    #[Test]
    public function it_can_withdraw_money_when_balance_is_enough()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/accounts/{$this->account->id}/withdraw", [
            'amount' => 40,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'transaction' => [
                    'amount' => 40,
                    'type' => 'withdrawal',
                ],
            ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => 60,
        ]);
    }

    #[Test]
    public function it_denies_withdraw_when_balance_is_not_enough()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/accounts/{$this->account->id}/withdraw", [
            'amount' => 300,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Withdrawal denied: insufficient balance',
            ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => 100,
        ]);
    }

    #[Test]
    public function it_allows_withdraw_using_overdraft_feature()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/accounts/{$this->account->id}/withdraw", [
            'amount' => 300,
            'features' => ['overdraft'],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => -200,
        ]);
    }

    #[Test]
    public function it_can_transfer_money_between_accounts()
    {
        $toAccount = Account::factory()->create([
            'balance' => 50,
            'user_id' => $this->user->id,
            'nickname' => 'Receiver Account',
        ]);

        $this->actingAs($this->user);

        $response = $this->postJson('/api/accounts/transfer', [
            'from_account_id' => $this->account->id,
            'to_account_id' => $toAccount->id,
            'amount' => 30,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => 70,
        ]);


        $this->assertDatabaseHas('accounts', [
            'id' => $toAccount->id,
            'balance' => 80,
        ]);
    }
}
