<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->nullable()->after('email');
            $table->string('referral_code', 10)->unique()->nullable()->after('phone');
            $table->string('referred_by')->nullable()->after('referral_code');
            $table->string('withdrawal_password')->nullable()->after('password');
            $table->enum('vip_level', ['0','1','2','3'])->default('0')->after('withdrawal_password');
            $table->boolean('is_blocked')->default(false)->after('vip_level');
            $table->string('device_id')->nullable()->after('is_blocked');
            $table->timestamp('last_login_at')->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone','referral_code','referred_by','withdrawal_password','vip_level','is_blocked','device_id','last_login_at']);
        });
    }
};
