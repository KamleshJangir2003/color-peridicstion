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

        $orderId       = $request->input('no') ?? $request->input('order_id');
        $status        = $request->input('status');
        $transactionId = $request->input('transaction_id') ?? $request->input('tx');
        $amount        = $request->input('amount');

        // Verify sign using only merchant_id, no, amount
        $signParams = [
            'merchant_id' => $request->input('merchant_id'),
            'no'          => $orderId,
            'amount'      => $amount,
        ];
        if (!$this->mvPayService->verifySign($signParams, $request->input('sign'))) {
            Log::warning('Payment Callback: Invalid signature', $request->all());
            return response('FAIL', 403);
        }

        $deposit = Deposit::where('id', $orderId)->first();

        if (!$deposit) {
            Log::warning('Payment Callback: Order not found', ['order_id' => $orderId]);
            return response('FAIL', 404);
        }

        if ($deposit->status !== 'pending') {
            return response('SUCCESS', 200);
        }

        if ($status === 'success' || $status === '1') {
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
            Log::info('Payment Callback: Deposit approved', ['deposit_id' => $deposit->id, 'amount' => $deposit->amount]);
        } else {
            $deposit->update(['status' => 'rejected']);
            Log::info('Payment Callback: Deposit rejected', ['deposit_id' => $deposit->id]);
        }

        return response('SUCCESS', 200);
    }

    // POST /api/payout/callback
    public function payoutCallback(Request $request)
    {
        Log::info('Payout Callback', $request->all());

        $orderId       = $request->input('no') ?? $request->input('order_id');
        $status        = $request->input('status');
        $transactionId = $request->input('transaction_id') ?? $request->input('tx');
        $amount        = $request->input('amount');

        // Verify sign using only merchant_id, no, amount
        $signParams = [
            'merchant_id' => $request->input('merchant_id'),
            'no'          => $orderId,
            'amount'      => $amount,
        ];
        if (!$this->mvPayService->verifySign($signParams, $request->input('sign'))) {
            Log::warning('Payout Callback: Invalid signature', $request->all());
            return response('FAIL', 403);
        }

        $withdrawal = Withdrawal::find($orderId);

        if (!$withdrawal) {
            return response('FAIL', 404);
        }

        if ($withdrawal->status !== 'pending') {
            return response('SUCCESS', 200);
        }

        if ($status === 'success' || $status === '1') {
            $withdrawal->update([
                'status'         => 'approved',
                'transaction_id' => $transactionId ?? $withdrawal->transaction_id,
            ]);
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

        return response('SUCCESS', 200);
    }
}
