<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearbyPlace extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_nearby_places')
            ->withPivot('distance_km', 'walking_time_mins', 'driving_time_mins')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Get icon based on type
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'airport' => 'fa-plane',
            'railway' => 'fa-train',
            'bus_station' => 'fa-bus',
            'metro' => 'fa-subway',
            'beach' => 'fa-umbrella-beach',
            'hospital' => 'fa-hospital',
            'mall' => 'fa-shopping-bag',
            'restaurant' => 'fa-utensils',
            'atm' => 'fa-credit-card',
            'temple' => 'fa-place-of-worship',
            'museum' => 'fa-landmark',
            'park' => 'fa-tree',
            'market' => 'fa-store',
            'pharmacy' => 'fa-prescription-bottle',
            default => 'fa-location-dot',
        };
    }

    // Get type display name
    public function getTypeDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->type));
    }

    // Get formatted distance
    public function getFormattedDistanceAttribute()
    {
        if ($this->distance_km < 1) {
            return round($this->distance_km * 1000) . ' m';
        }
        return number_format($this->distance_km, 1) . ' km';
    }
}
