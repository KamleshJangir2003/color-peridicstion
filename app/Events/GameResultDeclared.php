<?php

namespace App\Events;

use App\Models\GameResult;
use App\Models\GameRound;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameResultDeclared implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameRound $round,
        public GameResult $result
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('game')];
    }

    public function broadcastAs(): string
    {
        return 'result.declared';
    }

    public function broadcastWith(): array
    {
        return [
            'round_id' => $this->round->round_id,
            'number'   => $this->result->number,
            'color'    => $this->result->color,
        ];
    }
}
