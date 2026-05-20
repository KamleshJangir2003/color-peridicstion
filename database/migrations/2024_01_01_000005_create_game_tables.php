<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_id', 20)->unique(); // e.g. 20260520001
            $table->enum('status', ['open','closed','resulted'])->default('open');
            $table->enum('result_type', ['auto','admin','smart'])->default('smart');
            $table->tinyInteger('result_number')->nullable(); // 0-9
            $table->string('result_color')->nullable(); // green/red/violet
            $table->decimal('total_bet_amount', 15, 2)->default(0);
            $table->decimal('total_win_amount', 15, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('game_bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('round_id')->constrained('game_rounds')->cascadeOnDelete();
            $table->enum('bet_type', ['number','color']); // bet on number or color
            $table->string('bet_value'); // 0-9 or green/red/violet
            $table->decimal('amount', 15, 2);
            $table->decimal('win_amount', 15, 2)->default(0);
            $table->enum('status', ['pending','won','lost'])->default('pending');
            $table->timestamps();
        });

        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('game_rounds')->cascadeOnDelete();
            $table->tinyInteger('number'); // 0-9
            $table->string('color'); // green/red/violet
            $table->decimal('total_bets', 15, 2)->default(0);
            $table->decimal('total_payout', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_results');
        Schema::dropIfExists('game_bets');
        Schema::dropIfExists('game_rounds');
    }
};
