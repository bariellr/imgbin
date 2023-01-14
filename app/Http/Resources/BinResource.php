<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Auth;

class BinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $imagesCount = 0;
        // if (!$this->whenLoaded('images') instanceof MissingValue) {
        //     $imagesCount = $this->whenLoaded('images')->count();
        // }

        $hasUpvoted = false;
        if ($this->user_upvote_count !== null) {
            $hasUpvoted = $this->user_upvote_count > 0;
        }

        $hasDownvoted = false;
        if ($this->user_downvote_count !== null) {
            $hasDownvoted = $this->user_downvote_count > 0;
        }

        $hasFavorited = false;
        if ($this->user_favorite_count !== null) {
            $hasFavorited = $this->user_favorite_count > 0;
        }

        $allCommentsCount = 0;
        if ($this->all_comments_count !== null) {
            $allCommentsCount = $this->all_comments_count;
        }

        $allImagesCount = 0;
        if ($this->images_count !== null) {
            $allImagesCount = $this->images_count;
        }

        return [
            // attributes
            'id' => $this->nanoid,
            'title' => $this->title,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,

            // relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'cover_image' => new ImageResource($this->whenLoaded('coverImage')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'edit_hash' => new EditHashResource($this->whenLoaded('editHash')),

            // counts
            'favorites_count' => $this->favorites_count,
            'upvotes_count' => $this->upvotes_count,
            'downvotes_count' => $this->downvotes_count,
            'votes_sum' => $this->upvotes_count - $this->downvotes_count,
            'views_count' => $this->views_count,

            // conditional counts
            'images_count' => $allImagesCount,
            'comments_count' => $allCommentsCount,

            // user
            'has_upvoted' => $hasUpvoted,
            'has_downvoted' => $hasDownvoted,
            'has_favorited' => $hasFavorited,
        ];
    }
}
