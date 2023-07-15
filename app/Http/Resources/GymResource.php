<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
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
        $city_name = $this->city()->first()->city;
        return [
            "id" => $this->id,
            "manager_id" => $this->manager_id,
            "name" => $this->name,
            "about" => $this->about,
//            "city" => $this->city()->first()->city,
            "city" => ['value' => $city_name , 'label' => $city_name],
            "address" => $this->address,
            "avatar" => $this->avatar ? Storage::url('images/'. $this->avatar) : null,
            "image" => $this->image < 11 ? $this->image : Storage::url('images/'. $this->image),
            "lat" => $this->lat,
            "long" => $this->long,
            "tables_count" => $this->tables()->select(
                DB::raw('count(*) as count'),
            )->first()->count,
            "manager" => $this->whenLoaded('manager'),
            'tables' => $this->whenLoaded('tables'),
        ];
    }
}
