<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function open(Table $table)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $task = $table->tasks()->where('closed_at', null)->first();
        if (!$task) {
            $task = $table->tasks()->create([
                'price_so_far' => '0',
                'opened_at' => Carbon::now(),
            ]);
            $table->update([
                'status' => 1,
            ]);

        }
        $now = Carbon::now();
        $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
        $play_price = round(((int)(($table->price) / 60 * $play_time)), -2);
        return $this->success([
            'باز شد',
            'price_so_far' => $play_price,
            'duration' => $play_time,
            'opened_at' => substr($task->opened_at, 11, 5),
        ]);

    }

    public function close(Table $table)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $now = Carbon::now();
        $task = $table->tasks()->where('closed_at', null)->first();
        if ($task) {
            $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
            $play_price = round(((int)(($table->price) / 60 * $play_time)), -2);
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
                'opened_at' => substr($task->opened_at, 11, 5),
                'closed_at' => substr($task->closed_at, 11, 5),
            ]);
        } else {
            $task = $table->tasks()->orderBy('id', 'desc')->first();
            return $this->success([
                'قبلا بسته شده',
                'price' => $task->price_so_far,
                'duration' => Carbon::parse($task->opened_at)->diffInMinutes($task->closed_at),
                'opened_at' => substr($task->opened_at, 11, 5),
                'closed_at' => substr($task->closed_at, 11, 5),
            ]);
        }
    }
}
