<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistShare extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if share has been accepted
    public function isAccepted()
    {
        return $this->accepted_at !== null;
    }

    // Accept the share
    public function accept()
    {
        $this->update(['accepted_at' => now()]);
        return $this;
    }

    // Check permission
    public function canEdit()
    {
        return $this->permission === 'edit';
    }
}
