<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property int $user_id
 * @property string $type
 * @property float $amount
 * @property string $status
 * @property string|null $description
 * @property int|null $to_account_id
 * @property string|null $approved_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Account $account
 * @property-read Account|null $toAccount
 * @property-read User $user
 */
class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'user_id',
        'type',
        'amount',
        'status',
        'description',
        'to_account_id',
        'approved_type',

    ];

    /**
     * @return BelongsTo<Account, Transaction>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<Account, Transaction>
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    /**
     * @return BelongsTo<User, Transaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approve(string $type): void
    {
        $this->approved_type = $type;
        $this->status = 'approved';
        $this->save();
    }
}
