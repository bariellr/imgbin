<?php

namespace App\Http\Resources;

use App\Http\Resources\BinResource;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            // attributes
            'id' => $this->nanoid,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'profile_image' => $this->profile_image,
            'profile_image_url' => $this->profile_image_url,

            // relationships
            'bins' => BinResource::collection($this->whenLoaded('bins')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
