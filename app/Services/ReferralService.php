<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\ReferralCommission;
use App\Models\Setting;
use App\Models\User;

class ReferralService
{
    public function __construct(private WalletService $walletService) {}

    public function registerReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();
        if (!$referrer) return;

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'level'       => 1,
        ]);

        // Level 2 referral
        $level2 = Referral::where('referred_id', $referrer->id)->first();
        if ($level2) {
            Referral::create([
                'referrer_id' => $level2->referrer_id,
                'referred_id' => $newUser->id,
                'level'       => 2,
            ]);
        }
    }

    public function distributeCommission(int $fromUserId, float $betAmount): void
    {
        $commissions = [
            1 => (float) Setting::get('referral_commission_l1', 2),  // 2%
            2 => (float) Setting::get('referral_commission_l2', 1),  // 1%
        ];

        $referrals = Referral::where('referred_id', $fromUserId)
            ->whereIn('level', [1, 2])
            ->get();

        foreach ($referrals as $referral) {
            $rate = $commissions[$referral->level] ?? 0;
            if ($rate <= 0) continue;

            $commission = round($betAmount * $rate / 100, 2);

            ReferralCommission::create([
                'user_id'      => $referral->referrer_id,
                'from_user_id' => $fromUserId,
                'amount'       => $commission,
                'level'        => $referral->level,
                'source'       => 'bet',
            ]);

            $this->walletService->credit(
                $referral->referrer_id, $commission, 'bonus',
                "Level {$referral->level} referral commission"
            );
        }
    }
}
