<?php

namespace App\Services;

use App\Models\DailyBonus;
use App\Models\Setting;

class DailyBonusService
{
    public function __construct(private WalletService $walletService) {}

    public function claim(int $userId): array
    {
        $today = today();

        if (DailyBonus::where('user_id', $userId)->where('bonus_date', $today)->exists()) {
            return ['success' => false, 'message' => 'Already claimed today'];
        }

        $yesterday = DailyBonus::where('user_id', $userId)
            ->where('bonus_date', $today->copy()->subDay())
            ->first();

        $consecutive = $yesterday ? $yesterday->consecutive_days + 1 : 1;

        // Bonus amount increases with consecutive days (max 7)
        $baseBonus  = (float) Setting::get('daily_bonus_base', 10);
        $amount     = $baseBonus * min($consecutive, 7);

        DailyBonus::create([
            'user_id'          => $userId,
            'bonus_date'       => $today,
            'consecutive_days' => $consecutive,
            'amount'           => $amount,
        ]);

        $this->walletService->credit($userId, $amount, 'bonus', 'Daily check-in bonus');

        return ['success' => true, 'amount' => $amount, 'consecutive_days' => $consecutive];
    }
}
