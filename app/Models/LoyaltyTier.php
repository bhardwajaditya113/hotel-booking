<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(UserLoyalty::class);
    }

    public function rewards()
    {
        return $this->hasMany(LoyaltyReward::class, 'min_tier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get multiplier as decimal (1.5 instead of 150)
    public function getMultiplierDecimalAttribute()
    {
        return $this->points_multiplier / 100;
    }

    // Get next tier
    public function getNextTierAttribute()
    {
        return self::active()
            ->where('min_points', '>', $this->min_points)
            ->orderBy('min_points')
            ->first();
    }

    // Create default tiers
    public static function createDefaultTiers()
    {
        $tiers = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'icon' => 'fa-medal',
                'color' => '#CD7F32',
                'min_points' => 0,
                'min_bookings' => 0,
                'points_multiplier' => 100,
                'discount_percentage' => 0,
                'benefits' => [
                    'Early access to deals',
                    'Birthday bonus points',
                    'Email offers',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'icon' => 'fa-medal',
                'color' => '#C0C0C0',
                'min_points' => 1000,
                'min_bookings' => 3,
                'points_multiplier' => 125,
                'discount_percentage' => 5,
                'benefits' => [
                    'All Bronze benefits',
                    '5% off all bookings',
                    '1.25x points earning',
                    'Priority customer support',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'icon' => 'fa-crown',
                'color' => '#FFD700',
                'min_points' => 5000,
                'min_bookings' => 10,
                'points_multiplier' => 150,
                'discount_percentage' => 10,
                'benefits' => [
                    'All Silver benefits',
                    '10% off all bookings',
                    '1.5x points earning',
                    'Free room upgrades (when available)',
                    'Late checkout',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'icon' => 'fa-gem',
                'color' => '#E5E4E2',
                'min_points' => 15000,
                'min_bookings' => 25,
                'points_multiplier' => 200,
                'discount_percentage' => 15,
                'benefits' => [
                    'All Gold benefits',
                    '15% off all bookings',
                    '2x points earning',
                    'Guaranteed room upgrades',
                    'Free breakfast',
                    'Airport transfers',
                    'Personal concierge',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $tier) {
            self::updateOrCreate(['slug' => $tier['slug']], $tier);
        }
    }

    // Get tier by points
    public static function getTierByPoints($points)
    {
        return self::active()
            ->where('min_points', '<=', $points)
            ->orderByDesc('min_points')
            ->first() ?? self::active()->orderBy('min_points')->first();
    }
}
