<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function balance(Request $request)
    {
        return response()->json($this->walletService->getBalance($request->user()->id));
    }

    public function transactions(Request $request)
    {
        $transactions = $request->user()
            ->walletTransactions()
            ->latest()
            ->paginate(20);

        return response()->json($transactions);
    }
}
