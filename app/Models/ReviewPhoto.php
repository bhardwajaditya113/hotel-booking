<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewPhoto extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Get full URL for the photo
    public function getUrlAttribute()
    {
        return asset('upload/reviews/' . $this->photo_path);
    }
}
