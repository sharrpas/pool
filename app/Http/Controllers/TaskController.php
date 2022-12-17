<?php

namespace App\Http\Controllers;

use App\Models\Table;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function open(Table $table)
    {
        $task = $table->tasks()->where('closed_at', null)->first();
        if (!$task) {
            $table->tasks()->create([
                'price_so_far' => '0',
                'opened_at' => Carbon::now(),
            ]);
            $table->update([
                'status' => 1,
            ]);
            return $this->success('باز شد');
        }
        else{
            $now = Carbon::now();
            $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
            $play_price = (int)(($table->price)/60*$play_time);
            return  $this->success([
                'price_so_far' => $play_price,
                'duration' => $play_time,
                'opened_at' => substr($task->opened_at,11,5),
            ]);
        }
    }

    public function close(Table $table)
    {
        $now = Carbon::now();
        $task = $table->tasks()->where('closed_at', null)->first();
        if ($task) {
            $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
            $play_price = (int)(($table->price)/60*$play_time);
            $task->update([
                'price_so_far' => $play_price,
                'closed_at' => $now,
            ]);
            $table->update([
                'status' => 0,
            ]);
            return $this->success([
                'بسته شد',
                'price' => $play_price,
                'duration' => $play_time,
            ]);
        }
        else{
            $task = $table->tasks()->orderBy('id','desc')->first();
            return $this->success([
                'قبلا بسته شده',
                'price' => $task->price_so_far,
                'opened_at' => substr($task->opened_at,11,5),
                'closed_at' => substr($task->closed_at,11,5),
            ]);
        }
    }
}
