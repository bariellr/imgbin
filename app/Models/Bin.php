<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\EditHash;
use App\Models\Image;
use App\Models\User;
use App\Services\NanoidService;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeVoted;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Bin extends Model implements Viewable
{
    use HasFactory, HasEagerLimit, InteractsWithViews, CanBeFavorited, CanBeVoted, HasTags;

    protected $guarded = [];

    protected $with = ['user', 'coverImage'];

    protected $withCount = ['views', 'images', 'upvotes', 'downvotes', 'favorites', 'userUpvote', 'userDownvote', 'userFavorite'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($bin) {
            $bin->nanoid = app()->make(NanoidService::class)->generate();
        });

        static::created(function ($bin) {
            if ($bin->user) {
                $bin->user->upvote($bin);
            }

            // edit hash
            if (!Auth::check() && session()->has('edit_hash')) {
                $bin->editHash()->create([
                    'hash' => session()->get('edit_hash')
                ]);
            }
        });

        static::deleting(function ($bin) {
            foreach ($bin->images as $image) {
                $image->delete();
            }
        });

        static::deleted(function ($bin) {
            $bin->resetStats();
        });
    }

    public function resetStats()
    {
        $this->allComments()->delete();
        $this->upvotes()->detach();
        $this->downvotes()->detach();
        $this->favorites()->detach();
        $this->views()->delete();
        $this->editHash()->delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class)->oldest();
    }

    public function coverImage()
    {
        return $this->hasOne(Image::class)->oldest()->limit(1);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->parent()
            ->latest();
    }

    public function allComments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function downvotes()
    {
        return $this->downvoters();
    }

    public function upvotes()
    {
        return $this->upvoters();
    }

    public function favorites()
    {
        return $this->favoriters();
    }

    public function userUpvote()
    {
        return $this->upvotes()->where('user_id', Auth::id());
    }

    public function userDownvote()
    {
        return $this->downvotes()->where('user_id', Auth::id());
    }

    public function userFavorite()
    {
        return $this->favorites()->where('user_id', Auth::id());
    }

    public function editHash()
    {
        return $this->hasOne(EditHash::class);
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '!=', null);
    }

    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function getDirectoryPathAttribute()
    {
        return "images/{$this->created_at->format('Y/m/d')}/{$this->nanoid}";
    }
}
