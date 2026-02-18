<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function amenities()
    {
        return $this->hasMany(Amenity::class, 'category_id');
    }

    public function activeAmenities()
    {
        return $this->amenities()->where('is_active', true)->orderBy('sort_order');
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

    // Create default categories
    public static function createDefaults()
    {
        $categories = [
            [
                'name' => 'Room Amenities',
                'slug' => 'room-amenities',
                'icon' => 'fa-bed',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bathroom',
                'slug' => 'bathroom',
                'icon' => 'fa-bath',
                'sort_order' => 2,
            ],
            [
                'name' => 'Media & Technology',
                'slug' => 'media-technology',
                'icon' => 'fa-tv',
                'sort_order' => 3,
            ],
            [
                'name' => 'Food & Drink',
                'slug' => 'food-drink',
                'icon' => 'fa-utensils',
                'sort_order' => 4,
            ],
            [
                'name' => 'Services',
                'slug' => 'services',
                'icon' => 'fa-concierge-bell',
                'sort_order' => 5,
            ],
            [
                'name' => 'Outdoor & View',
                'slug' => 'outdoor-view',
                'icon' => 'fa-mountain-sun',
                'sort_order' => 6,
            ],
            [
                'name' => 'Accessibility',
                'slug' => 'accessibility',
                'icon' => 'fa-wheelchair',
                'sort_order' => 7,
            ],
            [
                'name' => 'Safety & Security',
                'slug' => 'safety-security',
                'icon' => 'fa-shield-halved',
                'sort_order' => 8,
            ],
            [
                'name' => 'Recreation',
                'slug' => 'recreation',
                'icon' => 'fa-dumbbell',
                'sort_order' => 9,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'icon' => 'fa-briefcase',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            self::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
