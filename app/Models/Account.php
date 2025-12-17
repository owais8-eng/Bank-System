<?php

namespace App\Models;

use App\Domain\Accounts\StateResolver;
use App\Domain\Accounts\States\AccountState;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
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




    public function parent()
    {
        return $this->belongsTo(Account::class,'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changeState(string $newState): void
    {
        $currentState = StateResolver::resolve($this->state);

        if (!$currentState->canChangeStateTo($newState)) {
            throw new \Exception("Invalid state transition");
        }

        $this->update(['state' => $newState]);
    }
    public function isActive(): bool
    {
        return $this->state === 'active';
    }
}
