<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','phone','password','withdrawal_password',
        'referral_code','referred_by','vip_level','is_blocked','is_admin','device_id',
    ];

    protected $hidden = ['password','withdrawal_password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_blocked'        => 'boolean',
        'is_admin'          => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function bets()
    {
        return $this->hasMany(GameBet::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function commissions()
    {
        return $this->hasMany(ReferralCommission::class);
    }

    public function dailyBonuses()
    {
        return $this->hasMany(DailyBonus::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
