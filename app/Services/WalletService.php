<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(int $userId, float $amount, string $walletType = 'main', string $description = '', string $reference = ''): WalletTransaction
    {
        return DB::transaction(function () use ($userId, $amount, $walletType, $description, $reference) {
            $wallet = Wallet::lockForUpdate()->firstOrCreate(['user_id' => $userId]);
            $column = $walletType . '_balance';
            $before = $wallet->$column;
            $wallet->increment($column, $amount);

            return WalletTransaction::create([
                'user_id'        => $userId,
                'wallet_type'    => $walletType,
                'type'           => 'credit',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $before + $amount,
                'description'    => $description,
                'reference'      => $reference,
            ]);
        });
    }

    public function debit(int $userId, float $amount, string $walletType = 'main', string $description = '', string $reference = ''): WalletTransaction
    {
        return DB::transaction(function () use ($userId, $amount, $walletType, $description, $reference) {
            $wallet = Wallet::lockForUpdate()->where('user_id', $userId)->firstOrFail();
            $column = $walletType . '_balance';

            if ($wallet->$column < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $before = $wallet->$column;
            $wallet->decrement($column, $amount);

            return WalletTransaction::create([
                'user_id'        => $userId,
                'wallet_type'    => $walletType,
                'type'           => 'debit',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $before - $amount,
                'description'    => $description,
                'reference'      => $reference,
            ]);
        });
    }

    public function getBalance(int $userId): array
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $userId]);
        return [
            'main'    => $wallet->main_balance,
            'winning' => $wallet->winning_balance,
            'bonus'   => $wallet->bonus_balance,
            'total'   => $wallet->main_balance + $wallet->winning_balance + $wallet->bonus_balance,
        ];
    }
}
