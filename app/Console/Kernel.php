<?php

namespace App\Console;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        //token expire
        $schedule->command('sanctum:prune-expired --hours=2')->everyOddHour();


        //new day tasks
        $schedule->call(function () {
            $tables = \App\Models\Table::query()->where('status',1)->get();

            $sb2now = Carbon::now()->subMinute(2);
            $tables->map(function ($table) use ($sb2now){

                $task = $table->tasks()->where('closed_at', null)->first();
                $play_time = Carbon::parse($task->opened_at)->diffInMinutes($sb2now);
                $play_price = round(((int)(($table->price) / 60 * $play_time)), -2);
                $task->update([
                    'duration' => $play_time,
                    'price_so_far' => $play_price,
                    'closed_at' => $sb2now,
                ]);
                $table->update([
                    'status' => 0,
                ]);

                $task = $table->tasks()->create([
                    'price_so_far' => '0',
                    'opened_at' => Carbon::now(),
                ]);
                $table->update([
                    'status' => 1,
                ]);
            });

        })->dailyAt('00:01');
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
