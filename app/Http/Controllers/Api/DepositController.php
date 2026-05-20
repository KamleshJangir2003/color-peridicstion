<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:100',
            'method'         => 'required|in:upi,qr,tron_usdt',
            'transaction_id' => 'nullable|string',
            'screenshot'     => 'nullable|image|max:2048',
        ]);

        $path = $request->hasFile('screenshot')
            ? $request->file('screenshot')->store('deposits', 'public')
            : null;

        $deposit = Deposit::create([
            'user_id'        => $request->user()->id,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'transaction_id' => $request->transaction_id,
            'screenshot'     => $path,
        ]);

        return response()->json($deposit, 201);
    }

    public function index(Request $request)
    {
        return response()->json(
            $request->user()->deposits()->latest()->paginate(15)
        );
    }
}
