<?php

namespace App\Http\Resources;

use App\Models\Buffet;
use App\Models\TableTask;
use App\Services\GetBuffet;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $playerr = null;
        $task = $this->tasks()->where('closed_at', null)->first() ?? null;
        if ($task) {
            $now = Carbon::now();
            $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
            $play_price = round(((int)(($this->price) / 60 * $play_time)), -2);

            if ($task->buffet != null) {
                $finalBuffets = (new GetBuffet())->buffets($task);
            }
            $playerr = ($task->player()->first() ?? null);

        }


        return [
            'id' => $this->id,
            'gym_id' => $this->gym_id,
            'name' => $this->name,
            'pic' => $this->pic,
            'price' => $this->price,
            'status' => $this->status,

            'price_so_far' => $play_price ?? null,
            'duration' => $play_time ?? null,
            'opened_at' => $task ? substr($task->opened_at, 11, 5) : null,

            'player' => $playerr? UserResource::make($playerr) : null,

            'buffet' => $finalBuffets ?? null,


        ];
    }
}
