<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index(Request $request)
    {
        $withdrawals = Withdrawal::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return response()->json($withdrawals);
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        $withdrawal->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'admin_note'  => $request->note,
        ]);

        return response()->json(['message' => 'Withdrawal approved']);
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        // Refund amount back to wallet
        $this->walletService->credit(
            $withdrawal->user_id, $withdrawal->amount, 'winning',
            'Withdrawal rejected - refund', "withdrawal_{$withdrawal->id}"
        );

        $withdrawal->update([
            'status'      => 'rejected',
            'approved_by' => $request->user()->id,
            'admin_note'  => $request->note,
        ]);

        return response()->json(['message' => 'Withdrawal rejected and amount refunded']);
    }
}
