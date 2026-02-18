<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'owner_response_at' => 'datetime',
    ];

    protected $appends = ['average_rating', 'formatted_date'];

    // Calculate average of all rating dimensions
    public function getAverageRatingAttribute()
    {
        return round(($this->rating_overall + $this->rating_cleanliness + $this->rating_location + 
                $this->rating_service + $this->rating_value + $this->rating_amenities + 
                $this->rating_comfort) / 7, 1);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F Y');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function photos()
    {
        return $this->hasMany(ReviewPhoto::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithPhotos($query)
    {
        return $query->has('photos');
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating_overall', '>=', $minRating);
    }

    public function scopeByTripType($query, $tripType)
    {
        return $query->where('trip_type', $tripType);
    }

    // Check if user found this review helpful
    public function isHelpfulBy($userId)
    {
        return $this->helpfulVotes()->where('user_id', $userId)->exists();
    }

    // Mark as helpful
    public function markHelpful($userId)
    {
        if (!$this->isHelpfulBy($userId)) {
            $this->helpfulVotes()->create(['user_id' => $userId]);
            $this->increment('helpful_count');
            return true;
        }
        return false;
    }

    // Unmark as helpful
    public function unmarkHelpful($userId)
    {
        if ($this->isHelpfulBy($userId)) {
            $this->helpfulVotes()->where('user_id', $userId)->delete();
            $this->decrement('helpful_count');
            return true;
        }
        return false;
    }
}
