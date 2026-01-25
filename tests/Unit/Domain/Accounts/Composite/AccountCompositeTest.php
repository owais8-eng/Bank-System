<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Accounts\Composite;

use App\Domain\Accounts\Composite\AccountCompositeFactory;
use App\Domain\Accounts\Composite\AccountGroup;
use App\Domain\Accounts\Composite\AccountLeaf;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountCompositeTest extends TestCase
{
    use RefreshDatabase;

    private AccountCompositeFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new AccountCompositeFactory;
    }

    public function test_account_leaf_returns_correct_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000.00,
        ]);

        $leaf = new AccountLeaf($account);

        $this->assertEquals(1000.00, $leaf->getTotalBalance());
    }

    public function test_account_group_calculates_total_balance(): void
    {
        $user = User::factory()->create();
        $parentAccount = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000.00,
        ]);

        $child1 = Account::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentAccount->id,
            'balance' => 2000.00,
        ]);

        $child2 = Account::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentAccount->id,
            'balance' => 3000.00,
        ]);

        $group = new AccountGroup($parentAccount);
        $group->addChild(new AccountLeaf($child1));
        $group->addChild(new AccountLeaf($child2));

        $this->assertEquals(10000.00, $group->getTotalBalance());
    }

    public function test_account_group_can_add_and_remove_children(): void
    {
        $user = User::factory()->create();
        $account1 = Account::factory()->create(['user_id' => $user->id]);
        $account2 = Account::factory()->create(['user_id' => $user->id]);

        $group = new AccountGroup;
        $leaf1 = new AccountLeaf($account1);
        $leaf2 = new AccountLeaf($account2);

        $group->addChild($leaf1);
        $group->addChild($leaf2);

        $this->assertCount(2, $group->getChildren());

        $group->removeChild($leaf1);

        $this->assertCount(1, $group->getChildren());
    }

    public function test_composite_factory_builds_correct_structure(): void
    {
        $user = User::factory()->create();
        $parentAccount = Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000.00,
        ]);

        Account::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentAccount->id,
            'balance' => 500.00,
        ]);

        $component = $this->factory->buildComposite($parentAccount);

        $this->assertInstanceOf(AccountGroup::class, $component);
        $this->assertEquals(1500.00, $component->getTotalBalance());
    }

    public function test_leaf_account_can_perform_transaction_when_active(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'state' => 'active',
            'balance' => 1000.00,
        ]);

        $leaf = new AccountLeaf($account);

        $this->assertTrue($leaf->canPerformTransaction(500.00));
    }

    public function test_leaf_account_cannot_perform_transaction_when_frozen(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'state' => 'frozen',
            'balance' => 1000.00,
        ]);

        $leaf = new AccountLeaf($account);

        $this->assertFalse($leaf->canPerformTransaction(500.00));
    }
}
