<?php

namespace App\Models;

use App\Support\MediaUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    public function type()
    {
        return $this->belongsTo(RoomType::class, 'roomtype_id', 'id');
    }

    public function room_numbers()
    {
        return $this->hasMany(RoomNumber::class, 'rooms_id')->where('status', 'Active');
    }

    /**
     * Nightly inventory rows used by bookings (column: book_date).
     */
    public function roomBookedDates()
    {
        return $this->hasMany(RoomBookedDate::class, 'room_id');
    }

    /**
     * Rooms with no overlapping booked nights in [check_in, check_out).
     */
    public function scopeAvailableBetween($query, $checkIn, $checkOut): Builder
    {
        $start = Carbon::parse($checkIn)->toDateString();
        $endExclusive = Carbon::parse($checkOut);
        if ($endExclusive->lte(Carbon::parse($start))) {
            return $query;
        }
        $end = $endExclusive->copy()->subDay()->toDateString();

        return $query->whereDoesntHave('roomBookedDates', function ($q) use ($start, $end) {
            $q->whereBetween('book_date', [$start, $end]);
        });
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
            ->withPivot('is_paid', 'price', 'notes')
            ->withTimestamps();
    }

    // Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'room_tags');
    }

    // Nearby places (one row per point of interest linked to this room)
    public function nearbyPlaces()
    {
        return $this->hasMany(NearbyPlace::class);
    }

    // House rules rows for this room
    public function houseRules()
    {
        return $this->hasMany(HouseRule::class);
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
        return $this->hasMany(PricingRule::class);
    }

    // Multi images (existing)
    public function multiImages()
    {
        return $this->hasMany(MultiImage::class, 'rooms_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'Active')->orWhere('status', 1);
        });
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
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }

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
        return $this->approvedReviews()->avg('rating_overall') ?? 0;
    }

    // Get rating breakdown
    public function getRatingBreakdownAttribute()
    {
        $reviews = $this->approvedReviews;

        return [
            'overall' => round($reviews->avg('rating_overall') ?? 0, 1),
            'cleanliness' => round($reviews->avg('rating_cleanliness') ?? 0, 1),
            'location' => round($reviews->avg('rating_location') ?? 0, 1),
            'value' => round($reviews->avg('rating_value') ?? 0, 1),
            'service' => round($reviews->avg('rating_service') ?? 0, 1),
            'amenities' => round($reviews->avg('rating_amenities') ?? 0, 1),
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
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
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
        $date = Carbon::parse($date);
        $basePrice = $this->price;

        // Apply pricing rules
        foreach ($this->pricingRules()->active()->orderBy('priority')->get() as $rule) {
            if ($rule->appliesTo($date)) {
                $basePrice = $rule->applyTo($basePrice);
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
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();
        if ($checkOut->lte($checkIn)) {
            return true;
        }
        $end = $checkOut->copy()->subDay();

        $overlap = RoomBookedDate::where('room_id', $this->id)
            ->whereBetween('book_date', [$checkIn->toDateString(), $end->toDateString()])
            ->exists();

        return ! $overlap;
    }

    // Get available room count for date range
    public function getAvailableCount($checkIn, $checkOut)
    {
        if (! $this->isAvailable($checkIn, $checkOut)) {
            return 0;
        }
        $n = $this->room_numbers()->count();

        return $n > 0 ? $n : 1;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return '₹'.number_format($this->price, 0);
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

    public function getImageUrlAttribute(): string
    {
        return MediaUrl::resolve($this->image, 'upload/roomimg');
    }
}
