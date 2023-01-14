<?php

namespace App\Http\Resources;

use App\Http\Resources\BinResource;
use Illuminate\Http\Resources\Json\JsonResource;
use ParsedownExtra;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $parsedown = new ParsedownExtra();

        return [
            // attributes
            'id' => $this->nanoid,
            'description' => $this->description,
            'description_html' => $parsedown->text($this->description),
            'filename' => $this->filename,
            'width' => $this->width,
            'height' => $this->height,
            'url' => $this->url,
            'created_at' => $this->created_at,

            // relationships
            'bin' => new BinResource($this->whenLoaded('bin')),

            // counts
            'views_count' => $this->views_count,
        ];
    }
}
