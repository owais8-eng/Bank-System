<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'frequency',
        'next_run_at',
        'active'
    ];

    protected $casts = [
        'next_run_at' => 'date',
        'active' => 'boolean'
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function scopeDueToday($query)
    {
        return $query
            ->where('active', true)
            ->whereDate('next_run_at', '<=', now());
    }

    public function markAsProcessed(): void
    {
        $this->next_run_at = match ($this->frequency) {
            'daily'   => Carbon::parse($this->next_run_at)->addDay(),
            'weekly'  => Carbon::parse($this->next_run_at)->addWeek(),
            'monthly' => Carbon::parse($this->next_run_at)->addMonth(),
        };

        $this->save();
    }
}
