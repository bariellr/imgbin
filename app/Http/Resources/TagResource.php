<?php

namespace App\Http\Resources;

use App\Http\Resources\BinResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $allPublishedBinsCount = 0;
        if ($this->bins_count !== null) {
            $allPublishedBinsCount = $this->bins_count;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            // conditional counts
            'bins_count' => $allPublishedBinsCount,
        ];
    }
}
