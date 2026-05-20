<?php

namespace App\Game;

use App\Models\GameBet;
use App\Models\GameResult;
use App\Models\GameRound;
use App\Models\Setting;
use App\Services\WalletService;
use App\Events\GameResultDeclared;
use Illuminate\Support\Facades\DB;

class GameEngine
{
    // Color mapping: number => color
    const COLOR_MAP = [
        0 => 'violet',
        1 => 'green',
        2 => 'red',
        3 => 'green',
        4 => 'red',
        5 => 'violet',
        6 => 'red',
        7 => 'green',
        8 => 'red',
        9 => 'green',
    ];

    // Payout multipliers
    const PAYOUT = [
        'number' => 9,   // bet on exact number → 9x
        'color'  => 2,   // bet on color → 2x
    ];

    public function __construct(private WalletService $walletService) {}

    public function createRound(): GameRound
    {
        $roundId = now()->format('Ymd') . str_pad(
            GameRound::whereDate('created_at', today())->count() + 1,
            3, '0', STR_PAD_LEFT
        );

        return GameRound::create([
            'round_id'   => $roundId,
            'status'     => 'open',
            'result_type'=> Setting::get('result_type', 'smart'),
            'starts_at'  => now(),
            'ends_at'    => now()->addSeconds(30),
        ]);
    }

    public function placeBet(int $userId, int $roundId, string $betType, string $betValue, float $amount): GameBet
    {
        $round = GameRound::findOrFail($roundId);

        if ($round->status !== 'open') {
            throw new \Exception('Round is closed for betting');
        }

        $this->walletService->debit($userId, $amount, 'main', 'Game bet', "round_{$roundId}");

        $bet = GameBet::create([
            'user_id'   => $userId,
            'round_id'  => $roundId,
            'bet_type'  => $betType,
            'bet_value' => $betValue,
            'amount'    => $amount,
        ]);

        GameRound::where('id', $roundId)->increment('total_bet_amount', $amount);

        return $bet;
    }

    public function closeRound(GameRound $round): void
    {
        $round->update(['status' => 'closed']);
    }

    public function generateResult(GameRound $round, ?int $adminNumber = null): GameResult
    {
        $number = match ($round->result_type) {
            'admin'  => $adminNumber ?? random_int(0, 9),
            'auto'   => random_int(0, 9),
            'smart'  => $this->smartResult($round),
            default  => random_int(0, 9),
        };

        $color = self::COLOR_MAP[$number];

        return DB::transaction(function () use ($round, $number, $color) {
            $round->update([
                'status'        => 'resulted',
                'result_number' => $number,
                'result_color'  => $color,
            ]);

            $totalPayout = $this->distributeWinnings($round, $number, $color);

            $result = GameResult::create([
                'round_id'     => $round->id,
                'number'       => $number,
                'color'        => $color,
                'total_bets'   => $round->total_bet_amount,
                'total_payout' => $totalPayout,
                'profit'       => $round->total_bet_amount - $totalPayout,
            ]);

            broadcast(new GameResultDeclared($round, $result))->toOthers();

            return $result;
        });
    }

    private function smartResult(GameRound $round): int
    {
        $bets = GameBet::where('round_id', $round->id)->get();

        // No bets - return random number
        if ($bets->isEmpty()) {
            return random_int(0, 9);
        }

        $exposure = array_fill(0, 10, 0);

        foreach ($bets as $bet) {
            if ($bet->bet_type === 'number') {
                $exposure[(int)$bet->bet_value] += $bet->amount * self::PAYOUT['number'];
            } else {
                foreach (self::COLOR_MAP as $num => $col) {
                    if ($col === $bet->bet_value) {
                        $exposure[$num] += $bet->amount * self::PAYOUT['color'];
                    }
                }
            }
        }

        return array_search(min($exposure), $exposure);
    }

    private function distributeWinnings(GameRound $round, int $number, string $color): float
    {
        $bets = GameBet::where('round_id', $round->id)->get();
        $totalPayout = 0;

        foreach ($bets as $bet) {
            $won = false;

            if ($bet->bet_type === 'number' && (int)$bet->bet_value === $number) {
                $won = true;
                $winAmount = $bet->amount * self::PAYOUT['number'];
            } elseif ($bet->bet_type === 'color' && $bet->bet_value === $color) {
                $won = true;
                $winAmount = $bet->amount * self::PAYOUT['color'];
            }

            if ($won) {
                $bet->update(['status' => 'won', 'win_amount' => $winAmount]);
                $this->walletService->credit(
                    $bet->user_id, $winAmount, 'winning',
                    'Game win', "round_{$round->id}"
                );
                $totalPayout += $winAmount;
            } else {
                $bet->update(['status' => 'lost']);
            }
        }

        GameRound::where('id', $round->id)->update(['total_win_amount' => $totalPayout]);

        return $totalPayout;
    }
}
