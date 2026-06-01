<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\MvPayService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(private MvPayService $mvPayService) {}

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'method' => 'required|in:upi,qr,tron_usdt',
        ]);

        $deposit = Deposit::create([
            'user_id' => $request->user()->id,
            'amount'  => $request->amount,
            'method'  => $request->method,
            'status'  => 'pending',
        ]);

        $response = $this->mvPayService->createPayment([
            'order_id'   => $deposit->id,
            'amount'     => $deposit->amount,
            'remark'     => 'Deposit #' . $deposit->id,
            'return_url' => url('/'),
        ]);

        return response()->json([
            'deposit'     => $deposit,
            'payment_url' => $response['data']['pay_url'] ?? $response['data']['pay_link'] ?? null,
            'mvpay'       => $response,
        ], 201);
    }

    public function index(Request $request)
    {
        return response()->json(
            $request->user()->deposits()->latest()->paginate(15)
        );
    }
}
