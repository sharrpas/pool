<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TableTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'table_id' => $this->table_id,
            'price_so_far' => $this->price_so_far,
            'payment_status' => $this->payment_status,
            'opened_at' => substr($this->opened_at, 11, 5),
            'closed_at' => substr($this->closed_at, 11, 5),
        ];
    }
}
