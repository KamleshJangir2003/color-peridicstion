<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBonus extends Model
{
    protected $fillable = ['user_id','bonus_date','consecutive_days','amount'];

    protected $casts = ['bonus_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
