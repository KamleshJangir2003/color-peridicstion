<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->loadCount(['bets', 'referrals']);
        $user->bets_won        = $user->bets()->where('status', 'won')->count();
        $user->commission_total= $user->commissions()->sum('amount');

        return response()->json($user);
    }
}
