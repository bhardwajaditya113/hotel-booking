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
        'distance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
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
        return match ($this->type) {
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
        $km = $this->distance ?? $this->distance_km ?? 0;
        if ($km < 1) {
            return round($km * 1000).' m';
        }

        return number_format((float) $km, 1).' km';
    }
}
