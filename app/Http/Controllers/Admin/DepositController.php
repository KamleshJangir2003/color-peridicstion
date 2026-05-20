<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\WalletService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index(Request $request)
    {
        $deposits = Deposit::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return response()->json($deposits);
    }

    public function approve(Request $request, Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        $deposit->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'admin_note'  => $request->note,
        ]);

        $this->walletService->credit(
            $deposit->user_id, $deposit->amount, 'main',
            'Deposit approved', "deposit_{$deposit->id}"
        );

        return response()->json(['message' => 'Deposit approved']);
    }

    public function reject(Request $request, Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        $deposit->update([
            'status'      => 'rejected',
            'approved_by' => $request->user()->id,
            'admin_note'  => $request->note,
        ]);

        return response()->json(['message' => 'Deposit rejected']);
    }
}
