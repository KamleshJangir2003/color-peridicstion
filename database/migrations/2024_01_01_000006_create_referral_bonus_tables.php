<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('level')->default(1); // 1,2,3 for multi-level
            $table->timestamps();
        });

        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // who earns
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete(); // who triggered
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('level');
            $table->string('source')->default('bet'); // bet/deposit
            $table->timestamps();
        });

        Schema::create('daily_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('bonus_date');
            $table->tinyInteger('consecutive_days')->default(1);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->unique(['user_id','bonus_date']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('model')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('data')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('daily_bonuses');
        Schema::dropIfExists('referral_commissions');
        Schema::dropIfExists('referrals');
    }
};
