<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Services\OtpService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(
        private WalletService $walletService,
        private OtpService $otpService
    ) {}

    public function store(Request $request)
    {
        $minLimit   = (float) Setting::get('withdrawal_min', 5);
        $dailyLimit = (float) Setting::get('withdrawal_daily_limit', 10000);

        $request->validate([
            'amount'          => "required|numeric|min:{$minLimit}",
            'method'          => 'required|in:bank,upi,tron',
            'account_details' => 'required|array',
            'otp'             => 'required|digits:6',
        ]);

        $user = $request->user();

        // Verify OTP
        $otpTarget = $user->email ?: $user->phone;
        if (!$this->otpService->verify($otpTarget, $request->otp, 'withdrawal')) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        // Check winning balance
        $wallet = $user->wallet;
        if (!$wallet || $wallet->winning_balance < $request->amount) {
            return response()->json(['message' => 'Insufficient winning balance'], 422);
        }

        // Check daily limit
        $todayTotal = Withdrawal::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');

        if ($todayTotal + $request->amount > $dailyLimit) {
            return response()->json(['message' => "Daily withdrawal limit ₹{$dailyLimit} exceeded"], 422);
        }

        $this->walletService->debit($user->id, $request->amount, 'winning', 'Withdrawal request');

        $withdrawal = Withdrawal::create([
            'user_id'         => $user->id,
            'amount'          => $request->amount,
            'method'          => $request->method,
            'account_details' => $request->account_details,
        ]);

        return response()->json(['withdrawal' => $withdrawal], 201);
    }

    public function index(Request $request)
    {
        return response()->json(
            $request->user()->withdrawals()->latest()->paginate(15)
        );
    }
}
