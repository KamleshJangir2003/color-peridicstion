<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id','main_balance','winning_balance','bonus_balance'];

    protected $casts = [
        'main_balance'    => 'decimal:2',
        'winning_balance' => 'decimal:2',
        'bonus_balance'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id', 'user_id');
    }
}
