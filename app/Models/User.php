<?php

namespace App\Models;

use App\Models\Bin;
use App\Services\NanoidService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Multicaret\Acquaintances\Traits\CanFavorite;
use Multicaret\Acquaintances\Traits\CanVote;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasEagerLimit, CanFavorite, CanVote;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $append = ['profile_image_url'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->nanoid = app()->make(NanoidService::class)->generate();
        });
    }

    public function bins()
    {
        return $this->hasMany(Bin::class)->latest();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ?
            route('user.profileImage', $this) :
            "https://ui-avatars.com/api/?name=" .
            urlencode($this->name) . "&background=0D8ABC&color=fff";
    }

    public function getProfileImagePathAttribute()
    {
        return "profile_images/{$this->profile_image}";
    }

    public function getBinPointsAttribute()
    {
        $bins = $this->bins;

        $upvotes = $bins->sum('upvotes_count');
        $downvotes = $bins->sum('downvotes_count');

        return $upvotes - $downvotes;
    }
}
