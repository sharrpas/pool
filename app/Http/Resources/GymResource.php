<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GymResource extends JsonResource
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
            "id" => $this->id,
            "manager_id" => $this->manager_id,
            "name" => $this->name,
            "about" => $this->about,
            "city" => $this->city()->first()->city,
            "address" => $this->address,
            "avatar" => Storage::url('images/'. $this->avatar),
            "image" => Storage::url('images/'. $this->image),
            "lat" => $this->lat,
            "long" => $this->long,
            'tables' => $this->whenLoaded('tables'),
        ];
    }
}
