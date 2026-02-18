<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_promoted' => 'boolean',
        'instant_book_enabled' => 'boolean',
        'verified_at' => 'datetime',
        'promoted_until' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_rating' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function activeRooms()
    {
        return $this->hasMany(Room::class)->where('status', 'Active');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Room::class)
            ->where('reviews.is_approved', true);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeHotels($query)
    {
        return $query->where('listing_type', 'hotel');
    }

    public function scopeUniqueStays($query)
    {
        return $query->where('listing_type', 'unique_stay');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeNearLocation($query, $latitude, $longitude, $radiusKm = 10)
    {
        // Haversine formula for distance calculation
        return $query->selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radiusKm)->orderBy('distance');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isHotel()
    {
        return $this->listing_type === 'hotel';
    }

    public function isUniqueStay()
    {
        return $this->listing_type === 'unique_stay';
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zipcode,
            $this->country
        ]);
        return implode(', ', $parts);
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('upload/properties/' . $this->cover_image);
        }
        if ($this->images && count($this->images) > 0) {
            return asset('upload/properties/' . $this->images[0]);
        }
        return asset('upload/no_image.jpg');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function updateRatingStats()
    {
        $reviews = $this->reviews;
        $this->update([
            'average_rating' => $reviews->avg('rating_overall') ?? 0,
            'total_reviews' => $reviews->count()
        ]);
    }
}
