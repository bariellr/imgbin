<?php

namespace App\Models;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditHash extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
