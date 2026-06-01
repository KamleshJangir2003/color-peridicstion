<?php

use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\GameWebController;
use Illuminate\Support\Facades\Route;

// Redirect root
Route::get('/', fn() => view('landing'))->name('home');

// User Auth Pages
Route::get('/login',           fn() => view('auth.login'))->name('login');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('forgot-password');
Route::post('/logout',         fn() => redirect('/login'))->name('logout');

// User Pages
Route::middleware('web.auth')->group(function () {
    Route::get('/game',     [GameWebController::class, 'game'])->name('game');
    Route::get('/wallet',   [GameWebController::class, 'wallet'])->name('wallet');
    Route::get('/deposit',  [GameWebController::class, 'deposit'])->name('deposit');
    Route::get('/withdraw', [GameWebController::class, 'withdraw'])->name('withdraw');
    Route::get('/profile',  [GameWebController::class, 'profile'])->name('profile');
});

// Admin Auth Page
Route::get('/admin/login', fn() => view('admin.login'))->name('admin.login');
Route::post('/admin/logout', fn() => redirect('/admin/login'))->name('admin.logout');

// Admin Pages
Route::prefix('admin')->name('admin.')->middleware('web.auth')->group(function () {
    Route::get('/dashboard',   [AdminWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',       [AdminWebController::class, 'users'])->name('users');
    Route::get('/deposits',    [AdminWebController::class, 'deposits'])->name('deposits');
    Route::get('/withdrawals', [AdminWebController::class, 'withdrawals'])->name('withdrawals');
    Route::get('/game',        [AdminWebController::class, 'game'])->name('game');
    Route::get('/live-bets',   [AdminWebController::class, 'liveBets'])->name('live-bets');
    Route::get('/settings',    [AdminWebController::class, 'settings'])->name('settings');
});
