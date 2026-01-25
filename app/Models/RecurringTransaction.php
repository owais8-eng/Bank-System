<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'user_id',
        'type',
        'amount',
        'frequency',
        'next_run',
        'description',
        'is_active',
    ];

    protected $casts = [
        'next_run' => 'datetime',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Account, RecurringTransaction>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<User, RecurringTransaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
