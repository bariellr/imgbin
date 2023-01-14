<?php

namespace App\Models;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag as BaseTag;

class Tag extends BaseTag
{
    public function bins()
    {
        return $this->morphedByMany(Bin::class, 'taggable');
    }

    public static function findBySlug(string $slug, string $type = null, string $locale = null)
    {
        $locale = $locale ?? static::getLocale();

        return static::query()
            ->where("slug->{$locale}", $slug)
            ->where('type', $type)
            ->first();
    }
}
