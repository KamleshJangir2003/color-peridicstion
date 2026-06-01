<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\MvPayService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function __construct(
        private WalletService $walletService,
        private MvPayService $mvPayService
    ) {}

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

        $details = $withdrawal->account_details;
        $method  = $withdrawal->method;

        $payout = $this->mvPayService->createPayout([
            'order_id'     => (string) $withdrawal->id,
            'amount'       => $withdrawal->amount,
            'bank_account' => $method === 'upi'
                                ? ($details['upi_id'] ?? '')
                                : ($details['account_number'] ?? ''),
            'bank_ifsc'    => $method === 'bank' ? ($details['ifsc'] ?? '') : '',
            'account_name' => $details['name'] ?? $withdrawal->user->name,
            'remark'       => 'Withdrawal #' . $withdrawal->id,
        ]);

        Log::info('Payout response for withdrawal #' . $withdrawal->id, $payout);

        $success = isset($payout['code']) && (string)$payout['code'] === '200';
        $success = $success || (isset($payout['status']) && strtolower($payout['status']) === 'success');

        if (!$success) {
            return response()->json([
                'message' => 'Gateway payout failed: ' . ($payout['message'] ?? $payout['msg'] ?? json_encode($payout)),
                'gateway' => $payout,
            ], 422);
        }

        $withdrawal->update([
            'status'         => 'approved',
            'approved_by'    => $request->user()->id,
            'approved_at'    => now(),
            'admin_note'     => $request->note,
            'transaction_id' => $payout['data']['order_no'] ?? $payout['order_no'] ?? null,
        ]);

        return response()->json(['message' => 'Withdrawal approved and payout sent', 'gateway' => $payout]);
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
