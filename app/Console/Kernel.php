<?php

namespace App\Console;

use App\Jobs\ItemInWishlist;
use App\Models\Category;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function ()
        {
            DB::table('category_visits')->truncate();
            $categories=Category::all();
            foreach ( $categories as $category ) {
                $category->popular=0;
                $category->update();
            }
        })->monthly();
        $schedule->call(function ()
        {
            dispatch(new ItemInWishlist());
        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
