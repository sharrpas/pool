<?php

namespace App\Http\Resources;

use App\Models\Buffet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $task = $this->tasks()->where('closed_at', null)->first() ?? null;
        if ($task) {
            $now = Carbon::now();
            $play_time = Carbon::parse($task->opened_at)->diffInMinutes($now);
            $play_price = round(((int)(($this->price) / 60 * $play_time)), -2);

            if ($task->buffet != null) {
                $task_buffets = array_map('intval', explode(',', $task->buffet));
                sort($task_buffets);

                $buffets = Buffet::query()->whereIn('id', $task_buffets)->get();
                $ids = $buffets->pluck('id')->toArray();
                $buffetsInArray = BuffetResource::collection($buffets);
                $finalBuffets = collect($task_buffets)->map(function ($id) use ($ids, $buffetsInArray) {
                    return $buffetsInArray[array_search($id, array_values($ids))];
                });
            }
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
            'opened_at' => $task ? substr($task->opened_at, 11, 5) : null ,

            'buffet' => $finalBuffets ?? null,


        ];
    }
}
