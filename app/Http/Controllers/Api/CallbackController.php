<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Services\MvPayService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    public function __construct(
        private WalletService $walletService,
        private MvPayService $mvPayService
    ) {}

    // POST /api/payment/callback
    public function paymentCallback(Request $request)
    {
        Log::info('Payment Callback', $request->all());

        if (!$this->mvPayService->verifySign($request->all())) {
            Log::warning('Payment Callback: Invalid signature', $request->all());
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId       = $request->input('no') ?? $request->input('order_id');
        $status        = $request->input('status');
        $transactionId = $request->input('transaction_id');
            ->orWhere('transaction_id', $orderId)
            ->first();

        if (!$deposit) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($status === 'success') {
            $deposit->update([
                'status'         => 'approved',
                'transaction_id' => $transactionId ?? $deposit->transaction_id,
            ]);
            $this->walletService->credit(
                $deposit->user_id,
                $deposit->amount,
                'main',
                'Deposit via MvPay',
                $transactionId
            );
        } else {
            $deposit->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // POST /api/payout/callback
    public function payoutCallback(Request $request)
    {
        Log::info('Payout Callback', $request->all());

        if (!$this->mvPayService->verifySign($request->all())) {
            Log::warning('Payout Callback: Invalid signature', $request->all());
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId       = $request->input('no') ?? $request->input('order_id');
        $status        = $request->input('status');
        $transactionId = $request->input('transaction_id');

        $withdrawal = Withdrawal::find($orderId);

        if (!$withdrawal) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($withdrawal->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($status === 'success') {
            $withdrawal->update(['status' => 'approved']);
        } else {
            $withdrawal->update(['status' => 'rejected']);
            $this->walletService->credit(
                $withdrawal->user_id,
                $withdrawal->amount,
                'winning',
                'Payout failed - refund',
                $transactionId
            );
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
