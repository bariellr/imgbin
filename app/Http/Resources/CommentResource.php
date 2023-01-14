<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hasUpvoted = false;
        if ($this->user_upvote_count !== null) {
            $hasUpvoted = $this->user_upvote_count > 0;
        }

        $hasDownvoted = false;
        if ($this->user_downvote_count !== null) {
            $hasDownvoted = $this->user_downvote_count > 0;
        }

        $repliesCount = 0;
        if (!$this->whenLoaded('replies') instanceof MissingValue) {
            $repliesCount = $this->whenLoaded('replies')->count();
        }

        return [
            // attributes
            'id' => $this->nanoid,
            'content' => $this->content,
            'created_at' => $this->created_at,

            // relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),

            // required counts
            'upvotes_count' => $this->upvotes_count,
            'downvotes_count' => $this->downvotes_count,
            'votes_sum' => $this->upvotes_count - $this->downvotes_count,

            // conditional counts
            'replies_count' => $repliesCount,

            // user
            'has_upvoted' => $hasUpvoted,
            'has_downvoted' => $hasDownvoted,
        ];
    }
}
