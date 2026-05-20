<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        $admin = User::updateOrCreate(
            ['phone' => '9999999999'],
            [
                'name'          => 'Admin',
                'email'         => 'admin@colorwin.com',
                'phone'         => '9999999999',
                'password'      => Hash::make('admin@123'),
                'is_admin'      => true,
                'referral_code' => 'ADMIN001',
            ]
        );
        Wallet::firstOrCreate(['user_id' => $admin->id]);

        // Test User Account
        $user = User::updateOrCreate(
            ['phone' => '8888888888'],
            [
                'name'                => 'Test User',
                'email'               => 'user@colorwin.com',
                'phone'               => '8888888888',
                'password'            => Hash::make('user@123'),
                'withdrawal_password' => Hash::make('1234'),
                'referral_code'       => 'TESTUSER',
                'is_admin'            => false,
            ]
        );
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $wallet->update(['main_balance' => 1000, 'winning_balance' => 500]);
    }
}
