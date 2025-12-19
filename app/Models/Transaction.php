<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Transaction extends Model

{
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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function user()
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
