<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameBet;
use App\Models\GameResult;
use App\Models\WalletTransaction;

class PublicController extends Controller
{
    public function liveStats()
    {
        // Recent bets (all users)
        $recentBets = GameBet::with('user')
            ->latest()
            ->take(30)
            ->get()
            ->map(fn($b) => [
                'type'    => $b->status,
                'name'    => substr($b->user->name ?? 'User', 0, 1) . str_repeat('*', 3) . substr($b->user->phone ?? '0000', -2),
                'amount'  => $b->amount,
                'win_amt' => $b->win_amount,
                'bet_on'  => $b->bet_value,
                'bet_type'=> $b->bet_type,
                'status'  => $b->status,
                'time'    => $b->created_at->diffForHumans(),
            ]);

        // Recent results
        $recentResults = GameResult::latest()->take(20)->get()->map(fn($r) => [
            'number' => $r->number,
            'color'  => $r->color,
        ]);

        $online = max(100, GameBet::whereDate('created_at', today())->distinct('user_id')->count() * 3 + rand(50, 200));

        return response()->json([
            'recent_bets'    => $recentBets,
            'recent_results' => $recentResults,
            'online_players' => $online,
            'total_bets_today' => GameBet::whereDate('created_at', today())->count(),
        ]);
    }
}
