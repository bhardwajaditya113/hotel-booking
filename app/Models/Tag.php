<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_tags');
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

    // Create default tags
    public static function createDefaults()
    {
        $tags = [
            ['name' => 'Best Seller', 'slug' => 'best-seller', 'color' => '#10B981', 'icon' => 'fa-fire', 'sort_order' => 1],
            ['name' => 'Top Rated', 'slug' => 'top-rated', 'color' => '#F59E0B', 'icon' => 'fa-star', 'sort_order' => 2],
            ['name' => 'New', 'slug' => 'new', 'color' => '#3B82F6', 'icon' => 'fa-sparkles', 'sort_order' => 3],
            ['name' => 'Couple Friendly', 'slug' => 'couple-friendly', 'color' => '#EC4899', 'icon' => 'fa-heart', 'sort_order' => 4],
            ['name' => 'Family Friendly', 'slug' => 'family-friendly', 'color' => '#8B5CF6', 'icon' => 'fa-children', 'sort_order' => 5],
            ['name' => 'Business', 'slug' => 'business', 'color' => '#6366F1', 'icon' => 'fa-briefcase', 'sort_order' => 6],
            ['name' => 'Luxury', 'slug' => 'luxury', 'color' => '#D97706', 'icon' => 'fa-crown', 'sort_order' => 7],
            ['name' => 'Budget', 'slug' => 'budget', 'color' => '#059669', 'icon' => 'fa-piggy-bank', 'sort_order' => 8],
            ['name' => 'Pet Friendly', 'slug' => 'pet-friendly', 'color' => '#7C3AED', 'icon' => 'fa-paw', 'sort_order' => 9],
            ['name' => 'Eco Friendly', 'slug' => 'eco-friendly', 'color' => '#22C55E', 'icon' => 'fa-leaf', 'sort_order' => 10],
            ['name' => 'Romantic Getaway', 'slug' => 'romantic-getaway', 'color' => '#EF4444', 'icon' => 'fa-heart', 'sort_order' => 11],
            ['name' => 'Beach Access', 'slug' => 'beach-access', 'color' => '#06B6D4', 'icon' => 'fa-umbrella-beach', 'sort_order' => 12],
            ['name' => 'Mountain Retreat', 'slug' => 'mountain-retreat', 'color' => '#64748B', 'icon' => 'fa-mountain', 'sort_order' => 13],
            ['name' => 'City Center', 'slug' => 'city-center', 'color' => '#475569', 'icon' => 'fa-city', 'sort_order' => 14],
            ['name' => 'Local Cuisine', 'slug' => 'local-cuisine', 'color' => '#F97316', 'icon' => 'fa-utensils', 'sort_order' => 15],
        ];

        foreach ($tags as $tag) {
            $tag['is_active'] = true;
            self::updateOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
