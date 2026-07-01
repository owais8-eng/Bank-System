<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dw_transaction_facts', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('transaction_type', 32);
            $table->string('status', 32);
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('transactions_count')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('avg_amount', 14, 2)->default(0);
            $table->decimal('min_amount', 14, 2)->default(0);
            $table->decimal('max_amount', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['metric_date', 'transaction_type', 'status', 'account_id', 'user_id'],
                'dw_transaction_fact_unique'
            );
            $table->index(['metric_date', 'transaction_type'], 'dw_transaction_date_type_idx');
            $table->index(['metric_date', 'status'], 'dw_transaction_date_status_idx');
            $table->index(['account_id', 'metric_date'], 'dw_transaction_account_date_idx');
            $table->index(['user_id', 'metric_date'], 'dw_transaction_user_date_idx');
        });

        Schema::create('dw_account_daily_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('account_type', 32);
            $table->string('account_state', 32);
            $table->unsignedBigInteger('accounts_count')->default(0);
            $table->decimal('total_balance', 14, 2)->default(0);
            $table->decimal('avg_balance', 14, 2)->default(0);
            $table->decimal('min_balance', 14, 2)->default(0);
            $table->decimal('max_balance', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['metric_date', 'account_type', 'account_state'],
                'dw_account_snapshot_unique'
            );
            $table->index(['metric_date', 'account_type'], 'dw_account_date_type_idx');
            $table->index(['metric_date', 'account_state'], 'dw_account_date_state_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dw_account_daily_snapshots');
        Schema::dropIfExists('dw_transaction_facts');
    }
};
