<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AdminWebController extends Controller
{
    public function dashboard()   { return view('admin.dashboard'); }
    public function users()       { return view('admin.users'); }
    public function deposits()    { return view('admin.deposits'); }
    public function withdrawals() { return view('admin.withdrawals'); }
    public function game()        { return view('admin.game'); }
    public function liveBets()    { return view('admin.live-bets'); }
    public function settings()    { return view('admin.settings'); }
}
