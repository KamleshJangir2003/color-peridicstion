<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/otp/send',        [AuthController::class, 'sendOtp']);
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/admin/login',     [AuthController::class, 'adminLogin']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/public/stats',     [\App\Http\Controllers\Api\PublicController::class, 'liveStats']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Wallet
    Route::get('/wallet/balance',      [WalletController::class, 'balance']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Deposits
    Route::post('/deposits',    [DepositController::class, 'store']);
    Route::get('/deposits',     [DepositController::class, 'index']);

    // Withdrawals
    Route::post('/withdrawals', [WithdrawalController::class, 'store']);
    Route::get('/withdrawals',  [WithdrawalController::class, 'index']);

    // Game
    Route::get('/game/round',   [GameController::class, 'currentRound']);
    Route::post('/game/bet',    [GameController::class, 'placeBet']);
    Route::get('/game/history', [GameController::class, 'history']);
    Route::get('/game/my-bets', [GameController::class, 'myBets']);

    // Bonus
    Route::post('/bonus/daily',    [BonusController::class, 'claimDaily']);
    Route::get('/bonus/history',   [BonusController::class, 'history']);

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard',                    [Admin\UserController::class, 'dashboard']);
    Route::get('/users',                        [Admin\UserController::class, 'index']);
    Route::post('/users/{user}/toggle-block',   [Admin\UserController::class, 'toggleBlock']);
    Route::post('/users/{user}/wallet',         [Admin\UserController::class, 'updateWallet']);

    Route::get('/deposits',                     [Admin\DepositController::class, 'index']);
    Route::post('/deposits/{deposit}/approve',  [Admin\DepositController::class, 'approve']);
    Route::post('/deposits/{deposit}/reject',   [Admin\DepositController::class, 'reject']);

    Route::get('/withdrawals',                      [Admin\WithdrawalController::class, 'index']);
    Route::post('/withdrawals/{withdrawal}/approve',[Admin\WithdrawalController::class, 'approve']);
    Route::post('/withdrawals/{withdrawal}/reject', [Admin\WithdrawalController::class, 'reject']);

    Route::get('/game/rounds',                  [Admin\GameController::class, 'rounds']);
    Route::post('/game/rounds/{round}/result',  [Admin\GameController::class, 'setResult']);
    Route::post('/game/result-type',            [Admin\GameController::class, 'setResultType']);

    // Settings
    Route::post('/settings',         [Admin\UserController::class, 'saveSetting']);
    Route::post('/change-password',  [Admin\UserController::class, 'changePassword']);
});
