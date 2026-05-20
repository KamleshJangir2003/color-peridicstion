<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class GameWebController extends Controller
{
    public function game()    { return view('game.index'); }
    public function wallet()  { return view('game.wallet'); }
    public function deposit() { return view('game.deposit'); }
    public function withdraw(){ return view('game.withdraw'); }
    public function profile() { return view('game.profile'); }
}
