<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ReportTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'table_id' => $this->table_id,
            'sum_price_so_far' => $this->sum_price_so_far,
            'count' => $this->count,
            'date' => $this->date,
//            'opened_at' => substr($this->opened_at, 11, 5),
//            'closed_at' => substr($this->closed_at, 11, 5),
//            'duration' => Carbon::parse($this->opened_at)->diffInMinutes(Carbon::parse($this->closed_at))
        ];
    }
}
