<?php

namespace App\Http\Resources\User;

use App\Services\GetBuffet;
use Illuminate\Http\Resources\Json\JsonResource;

class TableOneTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($this->buffet != null) {
            $finalBuffets = (new GetBuffet())->buffets($this);
        }

        $playerr = ($this->player()->first() ?? ['name' => 'مهمان','username' => 'Guest'])['username'];

        return [
            'id' => $this->id,
            'gym_name' => $this->table()->first()->gym()->first()->name,
            'table_name' => $this->table()->first()->name,
            'player' => $playerr,
            'table_price' => $this->price_so_far,
            'buffet_price' => $this->buffet_price,
            'total_price' => $this->buffet_price + $this->price_so_far,
            'payment_status' => $this->payment_status,
            'opened_at' => substr($this->opened_at, 11, 5),
            'closed_at' => substr($this->closed_at, 11, 5),
            'buffet' => $finalBuffets ?? null,
        ];    }
}
