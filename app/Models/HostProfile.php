<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'languages_spoken' => 'array',
        'is_superhost' => 'boolean',
        'verified_at' => 'datetime',
        'superhost_since' => 'datetime',
        'average_rating' => 'decimal:2',
        'response_rate' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->hasManyThrough(Property::class, User::class, 'id', 'user_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    public function scopeCompany($query)
    {
        return $query->where('type', 'company');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeSuperhost($query)
    {
        return $query->where('is_superhost', true);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isIndividual()
    {
        return $this->type === 'individual';
    }

    public function isCompany()
    {
        return in_array($this->type, ['company', 'hotel_chain']);
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isSuperhost()
    {
        return $this->is_superhost === true;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('upload/host_profiles/' . $this->photo);
        }
        return asset('upload/no_image.jpg');
    }

    public function updateStats()
    {
        $properties = $this->properties()->active()->get();
        $bookings = Booking::whereIn('property_id', $properties->pluck('id'))->get();
        
        $reviews = Review::whereHas('room', function($q) use ($properties) {
            $q->whereIn('property_id', $properties->pluck('id'));
        })->approved()->get();

        $this->update([
            'total_properties' => $properties->count(),
            'total_bookings' => $bookings->count(),
            'average_rating' => $reviews->avg('overall_rating') ?? 0,
            'total_reviews' => $reviews->count(),
        ]);
    }

    public function checkSuperhostEligibility()
    {
        // Superhost criteria (Airbnb-style):
        // - At least 10 completed trips OR 3 completed trips and 100 nights
        // - 90% response rate
        // - Less than 1% cancellation rate
        // - 4.8+ average rating

        $properties = $this->properties()->active()->get();
        $completedBookings = Booking::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 1)
            ->where('check_out', '<', now())
            ->count();

        $cancelledBookings = Booking::whereIn('property_id', $properties->pluck('id'))
            ->where('status', 2)
            ->count();

        $totalBookings = $completedBookings + $cancelledBookings;
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;

        $eligible = 
            ($completedBookings >= 10 || ($completedBookings >= 3 && $this->total_bookings >= 100)) &&
            ($this->response_rate >= 90) &&
            ($cancellationRate < 1) &&
            ($this->average_rating >= 4.8);

        if ($eligible && !$this->is_superhost) {
            $this->update([
                'is_superhost' => true,
                'superhost_since' => now()
            ]);
        } elseif (!$eligible && $this->is_superhost) {
            $this->update([
                'is_superhost' => false,
                'superhost_since' => null
            ]);
        }

        return $eligible;
    }
}
