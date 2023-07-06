<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BuffetResource;
use App\Models\Buffet;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $buffet_count = $this->buffet ? count(explode(',', $this->buffet)) : null;

        return [
            'id' => $this->id,
            'table_name' => $this->table->name,
            'gym_name' => $this->table->gym->name,
            'total_price' => $this->price_so_far + $this->buffet_price,
            'payment_status' => $this->payment_status,
            'opened_at' => substr($this->opened_at, 11, 5),
            'closed_at' => substr($this->closed_at, 11, 5),
            'buffet_count' => $buffet_count,

        ];
    }
}
