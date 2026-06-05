<?php

namespace App\Http\Controllers\Admin;

use App\Game\GameEngine;
use App\Http\Controllers\Controller;
use App\Models\GameRound;
use App\Models\Setting;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(private GameEngine $gameEngine) {}

    public function setResult(Request $request, GameRound $round)
    {
        $request->validate([
            'number' => 'required|integer|between:0,9',
        ]);

        if ($round->status === 'open') {
            return response()->json(['message' => 'Round is still open, wait for it to close'], 422);
        }

        if ($round->status === 'resulted') {
            return response()->json(['message' => 'Round already resulted'], 422);
        }

        $round->update(['result_type' => 'admin']);
        $result = $this->gameEngine->generateResult($round, $request->number);

        return response()->json($result);
    }

    public function setResultType(Request $request)
    {
        $request->validate(['type' => 'required|in:auto,admin,smart']);
        Setting::set('result_type', $request->type);
        return response()->json(['message' => 'Result type updated']);
    }

    public function rounds(Request $request)
    {
        return response()->json(
            GameRound::with('result')->latest()->paginate(20)
        );
    }

    public function liveBets()
    {
        $round = GameRound::where('status', 'open')->latest()->first();

        if (!$round) {
            return response()->json(['round' => null, 'bets' => [], 'summary' => [], 'total_amount' => 0]);
        }

        // Individual bets with user name & phone
        $bets = $round->bets()
            ->with('user:id,name,phone')
            ->orderByDesc('amount')
            ->get()
            ->map(fn($b) => [
                'user'      => $b->user->name ?? 'Unknown',
                'phone'     => $b->user->phone ?? '—',
                'bet_value' => $b->bet_value,
                'bet_type'  => $b->bet_type,
                'amount'    => $b->amount,
                'placed_at' => $b->created_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i:s A'),
            ]);

        // Summary grouped by bet_value
        $summary = $round->bets()
            ->selectRaw('bet_value, COUNT(*) as bet_count, SUM(amount) as total_amount')
            ->groupBy('bet_value')
            ->orderByDesc('total_amount')
            ->get();

        return response()->json([
            'round'        => $round->round_id,
            'ends_at'      => $round->ends_at,
            'bets'         => $bets,
            'summary'      => $summary,
            'total_amount' => $round->bets()->sum('amount'),
        ]);
    }
}
