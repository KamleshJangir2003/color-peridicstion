<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    protected $fillable = ['round_id','number','color','total_bets','total_payout','profit'];

    public function round()
    {
        return $this->belongsTo(GameRound::class, 'round_id');
    }
}
