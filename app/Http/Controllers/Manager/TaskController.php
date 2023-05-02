<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\TableTaskResource;
use App\Models\Buffet;
use App\Models\Table;
use App\Models\TableTask;
use Illuminate\Console\View\Components\Task;
use Illuminate\Support\Carbon;
use function GuzzleHttp\Promise\task;

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
            'table_id' => $table->id,
            'message' => $table->name . ' باز شد ',
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
                'duration' => $play_time,
                'price_so_far' => $play_price,
                'closed_at' => $now,
            ]);
            $table->update([
                'status' => 0,
            ]);
            return $this->success([
                'table_id' => $table->id,
                'message' => $table->name . ' بسته شد ',
                'price' => $play_price,
                'duration' => $play_time,
                'opened_at' => substr($task->opened_at, 11, 5),
                'closed_at' => substr($task->closed_at, 11, 5),
            ]);
        } else {
            $task = $table->tasks()->orderBy('id', 'desc')->first();
            return $this->success([
                'table_id' => $table->id,
                'message' => $table->name . ' قبلا بسته شده ',
                'price' => $task->price_so_far,
                'duration' => Carbon::parse($task->opened_at)->diffInMinutes($task->closed_at),
                'opened_at' => substr($task->opened_at, 11, 5),
                'closed_at' => substr($task->closed_at, 11, 5),
            ]);
        }
    }

    public function tasks(Table $table)
    {
        //todo return buffet
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $last24 = Carbon::now()->subDay();
        $tasks = $table->tasks()->where('opened_at', '>', $last24)->orderBy('opened_at', 'desc')->get();
        return $this->success(TableTaskResource::collection($tasks));
    }

    public function pay(TableTask $task)
    {
        if ($task->table()->first()->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        if ($task->closed_at == null)
            return $this->error(Status::OPERATION_ERROR, 'یادت رفته میز رو ببندی');

        $task->update([
            'payment_status' => 'paid',
        ]);

        return $this->success([
            'پرداخت شد',
            'table_id' => $task->table()->first()->id,
        ]);
    }

    public function unpaid(TableTask $task)
    {
        if ($task->table()->first()->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        if ($task->closed_at == null)
            return $this->error(Status::OPERATION_ERROR, 'یادت رفته میز رو ببندی');

        $task->update([
            'payment_status' => 'unpaid',
        ]);

        return $this->success([
            'وضعیت میز به عدم پرداخت تغییر یافت',
            'table_id' => $task->table()->first()->id,
        ]);
    }

    public function add_buffet(Table $table, Buffet $buffet)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        if ($buffet->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        if (!$task = $table->tasks()->where('closed_at', null)->first())
            return $this->error(Status::NOT_FOUND, 'برای گذاشتن محصول بوفه روی میز باید میز رو باز کنی');

        $task_buffet = $task->buffet ? array_map('intval', explode(',', $task->buffet)) : [];
        array_push($task_buffet, $buffet->id);

        $task_buffet_price = $task->buffet_price + $buffet->price;
        $task->update([
            'buffet' => implode(',', $task_buffet),
            'buffet_price' => $task_buffet_price,
        ]);
        return $this->success('یک ' . $buffet->title . ' روی میز ' . $table->name . ' اضافه شد.');
    }

    public function remove_buffet(Table $table, Buffet $buffet)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);
        if ($buffet->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        if (!$task = $table->tasks()->where('closed_at', null)->where('buffet', '!=', null)->first())
            return $this->error(Status::NOT_FOUND);

        $task_buffet = array_map('intval', explode(',', $task->buffet));

        $unset_value = array_search($buffet->id, $task_buffet);
        $task_buffet_price = $task->buffet_price;
        if ($unset_value != '') {
            unset($task_buffet[$unset_value]);
            $task_buffet_price -= $buffet->price;
        }

        $task->update([
            'buffet' => $task_buffet == null ? null : implode(',', array_values($task_buffet)),
            'buffet_price' => $task_buffet_price,
        ]);
        return $this->success($buffet->title . ' از روی میز ' . $table->name . ' حذف شد.');
    }

}
