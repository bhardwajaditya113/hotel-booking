<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'planned_check_in' => 'date',
        'planned_check_out' => 'date',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Get number of planned nights
    public function getPlannedNightsAttribute()
    {
        if ($this->planned_check_in && $this->planned_check_out) {
            return $this->planned_check_in->diffInDays($this->planned_check_out);
        }
        return null;
    }
}
