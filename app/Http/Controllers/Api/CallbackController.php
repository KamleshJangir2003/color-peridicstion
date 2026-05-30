<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    /**
     * Payment (Deposit) Callback
     * URL: POST /api/payment/callback
     *
     * Expected params from payment provider:
     * - order_id      : deposit ID ya transaction reference
     * - status        : "success" | "failed"
     * - amount        : amount in rupees
     * - transaction_id: payment provider ka transaction ID
     */
    public function paymentCallback(Request $request)
    {
        Log::info('Payment Callback Received', $request->all());

        $orderId       = $request->input('order_id');
        $status        = $request->input('status');
        $transactionId = $request->input('transaction_id');

        $deposit = Deposit::where('id', $orderId)
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
                'Deposit via payment gateway',
                $transactionId
            );
        } else {
            $deposit->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Payout (Withdrawal) Callback
     * URL: POST /api/payout/callback
     *
     * Expected params from payout provider:
     * - order_id      : withdrawal ID ya reference
     * - status        : "success" | "failed"
     * - transaction_id: payout provider ka transaction ID
     */
    public function payoutCallback(Request $request)
    {
        Log::info('Payout Callback Received', $request->all());

        $orderId       = $request->input('order_id');
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
            // Payout fail hua toh balance wapas karo
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
