<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Veer\Console\Kernel as VeerConsoleKernel;

class Kernel extends VeerConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);
        //
    }
}
