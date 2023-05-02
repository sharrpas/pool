<?php

namespace App\Http\Resources;

use App\Models\Buffet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TableTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $task_buffets = array_map('intval', explode(',', $this->buffet));

        $buffets = Buffet::query()->whereIn('id',$task_buffets)->get();
        $ids = $buffets->pluck('id')->toArray();
        $buffetsInArray = BuffetResource::collection($buffets);
        $finalBuffets = collect($task_buffets)->map(function ($id) use($ids, $buffetsInArray) {
            return $buffetsInArray[array_search($id, array_values($ids))];
        });

        $playerr = ($this->player()->first() ?? ['name' => 'مهمان','username' => 'Guest'])['username'];

        return [
            'id' => $this->id,
            'table_id' => $this->table_id,
            'player' => $playerr,
            'price_so_far' => $this->price_so_far,
            'payment_status' => $this->payment_status,
            'opened_at' => substr($this->opened_at, 11, 5),
            'closed_at' => substr($this->closed_at, 11, 5),
            'buffet' => $finalBuffets,
            'buffet_price' => $this->buffet_price,
        ];
    }
}
