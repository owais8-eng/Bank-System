<?php

namespace App\Services;

use App\Admin\Roles\RoleResolver;
use App\Domain\Accounts\Decorator\AccountAuthorizationFactory;
use App\Domain\Payments\PaymentGatewayFactory;
use App\Domain\Transaction\ManagerApprovalHandler;
use App\Domain\Transaction\Roles\RoleResolve;
use App\Domain\Transaction\TransactionValidator;
use App\Models\Account;
use App\Models\Transaction;
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
            $oldBalance = $account->balance;

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
            ActivityLogger::log(
                'Deposit transaction created',
                $transaction,
                auth()->user(),
                [
                    'amount' => $amount,
                    'old_balance' => $oldBalance,
                    'new_balance' => $account->balance,
                ],
                'transaction'
            );

            event(new TransactionCreated($transaction));

            return $transaction;
        });
    }

    public function withdraw(Account $account, float $amount, ?string $description = null)
    {
        if ($amount <= 0) {
            throw new DomainException("Amount must be positive");
        }

        $authorization = app(AccountAuthorizationFactory::class)->make($account);

        $authorization->authorizeWithdraw($amount);

        return DB::transaction(function () use ($account, $amount, $description) {

            $oldBalance = $account->balance;

            $account->balance -= $amount;
            $account->save();

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'user_id' => auth()->id(),
                'type' => 'withdrawal',
                'amount' => $amount,
                'status' => 'pending',
                'description' => $description,
            ]);

            ActivityLogger::log(
                'Withdrawal transaction created',
                $transaction,
                auth()->user(),
                [
                    'amount' => $amount,
                    'old_balance' => $oldBalance,
                    'new_balance' => $account->balance,
                ],
                'transaction'
            );
            event(new TransactionCreated($transaction));

            $gateway = PaymentGatewayFactory::make('legacy');

            if (! $gateway->process($transaction)) {
                throw new DomainException('External payment failed');
            }


            $transaction->update(['status' => 'approved']);

            event(new TransactionCreated($transaction));
            return $transaction;
        });
    }


    public function approve(Transaction $transaction, string $type): void
    {
        $transaction->update([
            'status' => 'approved',
            'approval_type' => $type,
        ]);
    }

    public function transfer(Account $from, Account $to, float $amount, ?string $description = null): Transaction
    {
        TransactionValidator::validate($from, $to, $amount);

        $role = RoleResolve::resolve(auth()->user()->role);

        if (!$role->canApproveTransaction($amount)) {
            throw new DomainException("User not authorized to approve this transaction");
        }

        return DB::transaction(function () use ($from, $to, $amount, $description) {

            $fromOldBalance = $from->balance;
            $toOldBalance   = $to->balance;

            $transaction = Transaction::create([
                'account_id'      => $from->id,
                'user_id'         => auth()->id(),
                'type'            => 'transfer',
                'to_account_id'   => $to->id,
                'amount'          => $amount,
                'status'          => 'pending',
                'description'     => $description,
                'approved_type'   => null,
            ]);

             ActivityLogger::log(
            'Transfer transaction created',
            $transaction,
            auth()->user(),
            [
                'amount' => $amount,
                'from_account' => $from->id,
                'to_account' => $to->id,
            ],
            'transaction'
        );

            app(TransactionApprovalService::class)->approve($transaction);

            $from->decrement('balance', $amount);
            $to->increment('balance', $amount);

            ActivityLogger::log(
            'From account balance updated',
            $from,
            auth()->user(),
            [
                'old_balance' => $fromOldBalance,
                'new_balance' => $from->balance,
            ],
            'account'
        );

        ActivityLogger::log(
            'To account balance updated',
            $to,
            auth()->user(),
            [
                'old_balance' => $toOldBalance,
                'new_balance' => $to->balance,
            ],
            'account'
        );
            return $transaction->fresh();
        });
    }
}
