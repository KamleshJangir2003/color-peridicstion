<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    protected $fillable = [
        'round_id','status','result_type','result_number',
        'result_color','total_bet_amount','total_win_amount','starts_at','ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function bets()
    {
        return $this->hasMany(GameBet::class, 'round_id');
    }

    public function result()
    {
        return $this->hasOne(GameResult::class, 'round_id');
    }
}
