<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Accounts\StateResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
    use HasFactory, Searchable, LogsActivity;
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

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'balance' => $this->balance,
            'state' => $this->state,
            'nickname' => $this->nickname,
            'owner_name' => $this->owner?->name,
            'owner_email' => $this->owner?->email,
            'created_at' => $this->created_at?->timestamp,
        ];
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'accounts_index';
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->isActive();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'type',
                'balance',
                'state',
                'nickname',
                'user_id',
                'parent_id',
                'daily_limit',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
