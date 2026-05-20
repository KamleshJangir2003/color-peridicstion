<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id','amount','method','account_details',
        'status','admin_note','approved_by','approved_at',
    ];

    protected $casts = [
        'account_details' => 'array',
        'approved_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
