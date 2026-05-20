<?php

use App\Jobs\ProcessGameRound;
use Illuminate\Support\Facades\Schedule;

// Game engine runs every minute (Laravel scheduler minimum is 1 min)
// For 30-second intervals, dispatch twice per minute
Schedule::job(new ProcessGameRound)->everyThirtySeconds();
