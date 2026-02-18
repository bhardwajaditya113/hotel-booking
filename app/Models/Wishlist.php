<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $appends = ['items_count', 'cover_url'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'wishlist_items')
            ->withPivot('notes', 'planned_check_in', 'planned_check_out')
            ->withTimestamps();
    }

    public function shares()
    {
        return $this->hasMany(WishlistShare::class);
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'wishlist_shares')
            ->withPivot('permission', 'invite_token', 'accepted_at')
            ->withTimestamps();
    }

    // Accessors
    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('upload/wishlists/' . $this->cover_image);
        }
        
        // Return first item's room image as cover
        $firstItem = $this->items()->with('room')->first();
        if ($firstItem && $firstItem->room) {
            return asset('upload/roomimg/' . $firstItem->room->image);
        }
        
        return asset('frontend/img/default-wishlist.jpg');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Methods
    public function addRoom($roomId, $notes = null, $checkIn = null, $checkOut = null)
    {
        return $this->items()->updateOrCreate(
            ['room_id' => $roomId],
            [
                'notes' => $notes,
                'planned_check_in' => $checkIn,
                'planned_check_out' => $checkOut,
            ]
        );
    }

    public function removeRoom($roomId)
    {
        return $this->items()->where('room_id', $roomId)->delete();
    }

    public function hasRoom($roomId)
    {
        return $this->items()->where('room_id', $roomId)->exists();
    }

    // Get or create default wishlist for user
    public static function getOrCreateDefault($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'is_default' => true],
            ['name' => 'My Favorites', 'privacy' => 'private']
        );
    }

    // Share wishlist
    public function shareWith($userId, $permission = 'view')
    {
        return $this->shares()->updateOrCreate(
            ['user_id' => $userId],
            [
                'permission' => $permission,
                'invite_token' => \Str::random(32),
            ]
        );
    }

    // Generate shareable link
    public function getShareableLinkAttribute()
    {
        if ($this->privacy === 'public') {
            return route('wishlist.public', ['id' => $this->id]);
        }
        
        $share = $this->shares()->whereNotNull('invite_token')->first();
        if ($share) {
            return route('wishlist.shared', ['token' => $share->invite_token]);
        }
        
        return null;
    }
}
