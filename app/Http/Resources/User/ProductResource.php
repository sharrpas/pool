<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $images = explode(',', $this->images);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'images' => array_map(function ($image) {
                return Storage::url('images/stores/' . $image);
            }, $images),
            'city' => $this->whenLoaded('gym')->city->city,
            'gym' => $this->whenLoaded('gym'),
        ];
    }
}
