<?php

namespace App\Jobs;

use App\Game\GameEngine;
use App\Models\GameRound;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGameRound implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GameEngine $gameEngine): void
    {
        // Close expired rounds and generate results
        $expiredRounds = GameRound::where('status', 'open')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($expiredRounds as $round) {
            $gameEngine->closeRound($round);
            // If admin mode, skip auto-result — admin will set it manually
            if ($round->result_type !== 'admin') {
                $gameEngine->generateResult($round);
            }
        }

        // Create new round only if no open or pending-admin-result round exists
        $hasActive = GameRound::whereIn('status', ['open', 'closed'])->exists();
        if (!$hasActive) {
            $gameEngine->createRound();
        }
    }
}
