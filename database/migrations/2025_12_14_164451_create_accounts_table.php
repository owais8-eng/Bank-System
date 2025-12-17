<?php

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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
                    $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->enum('type',['savings','checking','loan', 'investment']);
            $table->decimal('balance',12,2)->default(0);
            $table->enum('state',['active','frozen','suspended','closed'])->default('active');
            $table->foreignId('parent_id')->nullable()
                ->constrained('accounts')->nullOnDelete();
            $table->string('nickname')->nullable();
            $table->decimal('daily_limit', 12, 2)->nullable();
            $table->json('metadata')->nullable();

            $table->index('state');
            $table->index('parent_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
