<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Accounts\StateResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $type
 * @property float $balance
 * @property string $state
 * @property int|null $parent_id
 * @property string|null $nickname
 * @property float|null $daily_limit
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Account|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Account> $children
 * @property-read User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $transactions
 */
class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'balance',
        'state',
        'parent_id',
        'nickname',
        'daily_limit',

    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * @return BelongsTo<Account, Account>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * @return HasMany<Account>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * @return BelongsTo<User, Account>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changeState(string $newState): void
    {
        $currentState = StateResolver::resolve($this->state);

        if (! $currentState->canChangeStateTo($newState)) {
            throw new \Exception('Invalid state transition');
        }

        $this->update(['state' => $newState]);
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    /**
     * @return HasMany<Transaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
