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

        if ($round->status !== 'closed') {
            return response()->json(['message' => 'Round must be closed first'], 422);
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
}
