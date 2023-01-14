<?php

namespace App\Models;

use App\Models\Bin;
use App\Services\NanoidService;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Symfony\Component\Mime\MimeTypes;

class Image extends Model implements Viewable
{
    use HasFactory, HasEagerLimit, InteractsWithViews, CanBeFavorited;

    protected $guarded = [];

    protected $appends = ['url'];

    protected $withCount = ['views'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            $extension = MimeTypes::getDefault()->getExtensions($image->mime_type)[0] ?? 'png';
            $image->nanoid = app()->make(NanoidService::class)->generate();
            $image->filename = $image->nanoid . '.' . $extension;
        });

        static::deleting(function ($image) {
            $fs = app()->make(Filesystem::class);

            if ($fs->exists($image->full_path)) {
                $fs->delete($image->full_path);
            }
        });

        static::deleted(function ($image) {
            $image->views()->delete();
        });
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function getUrlAttribute()
    {
        return route('image.url', $this);
    }

    public function getFullPathAttribute()
    {
        return "{$this->bin->directory_path}/{$this->filename}";
    }
}
