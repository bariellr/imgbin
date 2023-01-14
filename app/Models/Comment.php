<?php

namespace App\Models;

use App\Models\User;
use App\Services\NanoidService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Multicaret\Acquaintances\Traits\CanBeVoted;

class Comment extends Model
{
    use HasFactory, CanBeVoted;

    protected $guarded = [];

    protected $with = ['user', 'replies'];

    protected $withCount = ['upvotes', 'downvotes', 'userUpvote', 'userDownvote'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            $comment->nanoid = app()->make(NanoidService::class)->generate();
        });

        static::created(function ($comment) {
            if ($comment->user) {
                $comment->user->upvote($comment);
            }
        });

        static::deleted(function ($comment) {
            $comment->allReplies()->delete();
            $comment->upvotes()->detach();
            $comment->downvotes()->detach();
        });
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('replies')
            ->latest();
    }

    public function allReplies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function upvotes()
    {
        return $this->upvoters();
    }

    public function downvotes()
    {
        return $this->downvoters();
    }

    public function userUpvote()
    {
        return $this->upvotes()->where('user_id', Auth::id());
    }

    public function userDownvote()
    {
        return $this->downvotes()->where('user_id', Auth::id());
    }

    public function scopeParent($query)
    {
        return $query->where('parent_id', '=', null);
    }
}
