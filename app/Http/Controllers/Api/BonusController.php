<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DailyBonusService;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function __construct(private DailyBonusService $bonusService) {}

    public function claimDaily(Request $request)
    {
        $result = $this->bonusService->claim($request->user()->id);

        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }

    public function history(Request $request)
    {
        return response()->json(
            $request->user()->dailyBonuses()->latest()->paginate(30)
        );
    }
}
