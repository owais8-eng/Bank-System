<?php

namespace App\Services;

use App\Admin\Roles\RoleResolver;
use App\Domain\Transaction\ManagerApprovalHandler;
use App\Domain\Transaction\Roles\RoleResolve;
use App\Domain\Transaction\RoleStrategy;
use App\Domain\Transaction\TransactionValidator;
use App\Models\Account;
use App\Models\Transaction;
use AutoApprovalHandler;
use Illuminate\Support\Facades\DB;
use DomainException;
use App\Events\TransactionCreated;

class TransactionService
{
    public function deposit(Account $account, float $amount, ?string $description = null)
    {
        if ($amount <= 0) {
            throw new DomainException("Amount must be positive");
        }

        return DB::transaction(function () use ($account, $amount, $description) {
            $account->balance += $amount;
            $account->save();

            $transaction =  Transaction::create([
                'account_id' => $account->id,
                'user_id' => auth()->id(),
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'approved',
                'description' => $description,
            ]);
            event(new TransactionCreated($transaction));

            return $transaction;

        });

    }

    public function withdraw(Account $account, float $amount, ?string $description = null)
    {
        if ($amount <= 0) {
            throw new DomainException("Amount must be positive");
        }

        if ($account->balance < $amount) {
            throw new DomainException("Insufficient balance");
        }

        return DB::transaction(function () use ($account, $amount, $description) {
            $account->balance -= $amount;
            $account->save();

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'user_id' => auth()->id(),
                'type' => 'withdrawal',
                'amount' => $amount,
                'status' => 'approved',
                'description' => $description,
            ]);
            event(new TransactionCreated($transaction));

            return $transaction;
        });
    }

    public function transfer(Account $from, Account $to, float $amount, ?string $description = null)
    {
        TransactionValidator::validate($from, $to, $amount);

        $role = RoleResolve::resolve(auth()->user()->role);

        if (!$role->canApproveTransaction($amount)) {
            throw new DomainException('User not authorized for this transaction');
        }

        $auto = new AutoApprovalHandler();
        $manager = new ManagerApprovalHandler();
        $auto->setNext($manager);

        $status = $auto->handle($amount);
        return DB::transaction(function () use ($from, $to, $amount, $status) {

            if ($status === 'approved') {
                $from->decrement('balance', $amount);
                $to->increment('balance', $amount);
            }

            $transaction = Transaction::create([
                'account_id' => $from->id,
                'to_account_id' => $to->id,
                'amount' => $amount,
                'status' => $status,
                'user_id' => auth()->id(),
                'type' => 'transfer',
            ]);
            event(new TransactionCreated($transaction));

            return $transaction;

        });
    }
}
