<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GameBet;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function dashboard()
    {
        return response()->json([
            'total_users'   => User::where('is_admin', false)->count(),
            'total_revenue' => GameBet::sum('amount') - \App\Models\GameResult::sum('total_payout'),
            'pending_deposits'   => Deposit::where('status', 'pending')->count(),
            'pending_withdrawals'=> Withdrawal::where('status', 'pending')->count(),
            'active_bets'   => GameBet::where('status', 'pending')->count(),
        ]);
    }

    public function index(Request $request)
    {
        $users = User::with('wallet')
            ->where('is_admin', false)
            ->when($request->search, fn($q) => $q->where('phone', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function toggleBlock(User $user)
    {
        $user->update(['is_blocked' => !$user->is_blocked]);
        return response()->json(['is_blocked' => $user->is_blocked]);
    }

    public function updateWallet(Request $request, User $user)
    {
        $request->validate([
            'amount'      => 'required|numeric',
            'type'        => 'required|in:credit,debit',
            'wallet_type' => 'required|in:main,winning,bonus',
            'note'        => 'nullable|string',
        ]);

        if ($request->type === 'credit') {
            $this->walletService->credit($user->id, $request->amount, $request->wallet_type, $request->note ?? 'Admin credit');
        } else {
            $this->walletService->debit($user->id, $request->amount, $request->wallet_type, $request->note ?? 'Admin debit');
        }

        return response()->json(['message' => 'Wallet updated', 'balance' => $this->walletService->getBalance($user->id)]);
    }

    public function getSettings()
    {
        $keys = ['round_duration','min_bet','max_bet','withdrawal_min','withdrawal_daily_limit',
                 'deposit_min','referral_commission_l1','referral_commission_l2',
                 'daily_bonus_base','upi_id','tron_address','qr_image'];
        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = \App\Models\Setting::get($k);
        }
        return response()->json(['settings' => $settings]);
    }

    public function uploadQr(Request $request)
    {
        $request->validate(['qr_image' => 'required|image|max:2048']);
        $path = $request->file('qr_image')->store('qr', 'public');
        \App\Models\Setting::set('qr_image', $path);
        return response()->json(['message' => 'QR uploaded', 'path' => $path]);
    }

    public function saveSetting(Request $request)
    {
        $request->validate(['key' => 'required|string', 'value' => 'required']);
        \App\Models\Setting::set($request->key, $request->value);
        return response()->json(['message' => 'Setting saved']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $request->user()->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);
        return response()->json(['message' => 'Password changed successfully']);
    }
}
