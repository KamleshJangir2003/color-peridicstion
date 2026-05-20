<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'user_id','amount','method','transaction_id',
        'screenshot','status','admin_note','approved_by','approved_at',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
