<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'instant_book' => 'boolean',
    ];

    public function type(){
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'id');
    }

    public function room_numbers(){
        return $this->hasMany(RoomNumber::class, 'rooms_id')->where('status','Active');
    }

    // ==========================================
    // NEW RELATIONSHIPS FOR ENHANCED FEATURES
    // ==========================================

    // Reviews for this room
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Approved reviews only
    public function approvedReviews()
    {
        return $this->reviews()->approved()->latest();
    }

    // Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'rooms_id');
    }

    // Property (hotel, home, etc.)
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Amenities
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities')
            ->withPivot('is_included', 'additional_price', 'notes')
            ->withTimestamps();
    }

    // Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'room_tags');
    }

    // Nearby places
    public function nearbyPlaces()
    {
        return $this->belongsToMany(NearbyPlace::class, 'room_nearby_places')
            ->withPivot('distance_km', 'walking_time_mins', 'driving_time_mins')
            ->withTimestamps();
    }

    // House rules
    public function houseRules()
    {
        return $this->belongsToMany(HouseRule::class, 'room_house_rules')
            ->withPivot('custom_value', 'is_allowed')
            ->withTimestamps();
    }

    // Cancellation policy
    public function cancellationPolicy()
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    // Wishlist items (users who wishlisted this room)
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    // Pricing rules
    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class)->active()->ordered();
    }

    // Multi images (existing)
    public function multiImages()
    {
        return $this->hasMany(MultiImage::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInstantBook($query)
    {
        return $query->where('instant_book', true);
    }

    public function scopeWithAmenity($query, $amenityId)
    {
        return $query->whereHas('amenities', function ($q) use ($amenityId) {
            $q->where('amenity_id', $amenityId);
        });
    }

    public function scopeWithTag($query, $tagSlug)
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    public function scopePriceRange($query, $min, $max)
    {
        if ($min) $query->where('price', '>=', $min);
        if ($max) $query->where('price', '<=', $max);
        return $query;
    }

    public function scopeCapacity($query, $guests)
    {
        return $query->where('total_adult', '>=', $guests);
    }

    // ==========================================
    // RATING & REVIEW METHODS
    // ==========================================

    // Get average rating
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('overall_rating') ?? 0;
    }

    // Get rating breakdown
    public function getRatingBreakdownAttribute()
    {
        $reviews = $this->approvedReviews;
        
        return [
            'overall' => round($reviews->avg('overall_rating') ?? 0, 1),
            'cleanliness' => round($reviews->avg('cleanliness_rating') ?? 0, 1),
            'location' => round($reviews->avg('location_rating') ?? 0, 1),
            'value' => round($reviews->avg('value_rating') ?? 0, 1),
            'service' => round($reviews->avg('service_rating') ?? 0, 1),
            'amenities' => round($reviews->avg('amenities_rating') ?? 0, 1),
            'count' => $reviews->count(),
        ];
    }

    // Get review count
    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    // ==========================================
    // PRICING METHODS
    // ==========================================

    // Calculate dynamic price for a date range
    public function calculatePrice($checkIn, $checkOut, $guests = 1)
    {
        $checkIn = \Carbon\Carbon::parse($checkIn);
        $checkOut = \Carbon\Carbon::parse($checkOut);
        $nights = $checkIn->diffInDays($checkOut);
        
        $totalPrice = 0;
        $currentDate = $checkIn->copy();
        
        while ($currentDate < $checkOut) {
            $nightPrice = $this->getPriceForDate($currentDate);
            $totalPrice += $nightPrice;
            $currentDate->addDay();
        }
        
        // Extra guest charges
        $baseGuests = $this->total_adult ?? 2;
        if ($guests > $baseGuests) {
            $extraGuests = $guests - $baseGuests;
            $extraCharge = ($this->extra_guest_charge ?? 0) * $extraGuests * $nights;
            $totalPrice += $extraCharge;
        }
        
        return [
            'nights' => $nights,
            'subtotal' => $totalPrice,
            'per_night_avg' => $nights > 0 ? round($totalPrice / $nights, 2) : 0,
            'taxes' => round($totalPrice * 0.18, 2), // 18% GST
            'total' => round($totalPrice * 1.18, 2),
        ];
    }

    // Get price for a specific date
    public function getPriceForDate($date)
    {
        $date = \Carbon\Carbon::parse($date);
        $basePrice = $this->price;
        
        // Apply pricing rules
        foreach ($this->pricingRules as $rule) {
            if ($rule->appliesTo($date)) {
                $basePrice = $rule->applyToPrice($basePrice);
            }
        }
        
        return $basePrice;
    }

    // ==========================================
    // AVAILABILITY METHODS
    // ==========================================

    // Check availability for date range
    public function isAvailable($checkIn, $checkOut)
    {
        $checkIn = \Carbon\Carbon::parse($checkIn);
        $checkOut = \Carbon\Carbon::parse($checkOut);
        
        // Get booked room numbers for this date range
        $bookedRoomNumbers = RoomBookedDate::where('booking_date', '>=', $checkIn)
            ->where('booking_date', '<', $checkOut)
            ->whereHas('roomNumber', function ($q) {
                $q->where('rooms_id', $this->id);
            })
            ->pluck('room_number_id')
            ->unique();
        
        // Check if any room numbers are available
        $totalRooms = $this->room_numbers()->count();
        $bookedCount = $bookedRoomNumbers->count();
        
        return $totalRooms > $bookedCount;
    }

    // Get available room count for date range
    public function getAvailableCount($checkIn, $checkOut)
    {
        $checkIn = \Carbon\Carbon::parse($checkIn);
        $checkOut = \Carbon\Carbon::parse($checkOut);
        
        $bookedRoomNumbers = RoomBookedDate::whereBetween('booking_date', [$checkIn, $checkOut->subDay()])
            ->whereHas('roomNumber', function ($q) {
                $q->where('rooms_id', $this->id);
            })
            ->pluck('room_number_id')
            ->unique();
        
        return $this->room_numbers()->count() - $bookedRoomNumbers->count();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->price, 0);
    }

    // Get featured amenities
    public function getFeaturedAmenitiesAttribute()
    {
        return $this->amenities()->where('is_featured', true)->limit(6)->get();
    }

    // Get amenities grouped by category
    public function getGroupedAmenitiesAttribute()
    {
        return $this->amenities()->with('category')->get()->groupBy('category.name');
    }

    // Get wishlist count
    public function getWishlistCountAttribute()
    {
        return $this->wishlistItems()->count();
    }
}
