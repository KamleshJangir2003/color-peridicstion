<?php

namespace App\Http\Controllers\Api;

use App\Game\GameEngine;
use App\Http\Controllers\Controller;
use App\Models\GameRound;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function __construct(
        private GameEngine $gameEngine,
        private ReferralService $referralService
    ) {}

    public function currentRound()
    {
        DB::transaction(function () {
            // Close & result all expired open rounds
            $expired = GameRound::where('status', 'open')
                ->where('ends_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($expired as $round) {
                $this->gameEngine->closeRound($round);
                $this->gameEngine->generateResult($round);
            }
        });

        // Get current open round
        $round = GameRound::where('status', 'open')->latest()->first();

        // Create new round if none
        if (!$round) {
            $round = $this->gameEngine->createRound();
        }

        $secondsLeft = max(0, (int) now()->diffInSeconds($round->ends_at, false));

        return response()->json([
            'id'           => $round->id,
            'round_id'     => $round->round_id,
            'ends_at'      => $round->ends_at->toISOString(),
            'seconds_left' => $secondsLeft,
            'status'       => $round->status,
        ]);
    }

    public function placeBet(Request $request)
    {
        $request->validate([
            'round_id'  => 'required|exists:game_rounds,id',
            'bet_type'  => 'required|in:number,color',
            'bet_value' => 'required|string',
            'amount'    => 'required|numeric|min:10',
        ]);

        // Validate round is still open and has time left
        $round = GameRound::findOrFail($request->round_id);

        if ($round->status !== 'open') {
            return response()->json(['message' => 'Round is closed. Please wait for next round.'], 422);
        }

        if ($round->ends_at <= now()) {
            return response()->json(['message' => 'Round time is up. Please wait for next round.'], 422);
        }

        // Validate bet value
        if ($request->bet_type === 'number') {
            if (!in_array($request->bet_value, ['0','1','2','3','4','5','6','7','8','9'])) {
                return response()->json(['message' => 'Invalid number. Choose 0-9.'], 422);
            }
        } else {
            if (!in_array($request->bet_value, ['green','red','violet'])) {
                return response()->json(['message' => 'Invalid color.'], 422);
            }
        }

        try {
            $bet = $this->gameEngine->placeBet(
                $request->user()->id,
                $request->round_id,
                $request->bet_type,
                $request->bet_value,
                $request->amount
            );

            $this->referralService->distributeCommission($request->user()->id, $request->amount);

            return response()->json($bet, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function history(Request $request)
    {
        return response()->json(
            \App\Models\GameResult::with('round')->latest()->paginate(20)
        );
    }

    public function myBets(Request $request)
    {
        return response()->json(
            $request->user()->bets()->with('round')->latest()->paginate(20)
        );
    }
}
