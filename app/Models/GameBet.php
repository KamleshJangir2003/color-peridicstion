<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameBet extends Model
{
    protected $fillable = [
        'user_id','round_id','bet_type','bet_value','amount','win_amount','status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function round()
    {
        return $this->belongsTo(GameRound::class, 'round_id');
    }
}
