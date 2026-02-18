<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(AmenityCategory::class, 'category_id');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities')
            ->withPivot('is_included', 'additional_price', 'notes')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeInCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        if (!$this->is_paid) return 'Free';
        return '₹' . number_format($this->price, 2);
    }

    // Create default amenities
    public static function createDefaults()
    {
        $amenities = [
            // Room Amenities
            ['category' => 'room-amenities', 'name' => 'Air Conditioning', 'icon' => 'fa-snowflake', 'sort_order' => 1],
            ['category' => 'room-amenities', 'name' => 'Heating', 'icon' => 'fa-temperature-arrow-up', 'sort_order' => 2],
            ['category' => 'room-amenities', 'name' => 'Ceiling Fan', 'icon' => 'fa-fan', 'sort_order' => 3],
            ['category' => 'room-amenities', 'name' => 'Iron & Board', 'icon' => 'fa-shirt', 'sort_order' => 4],
            ['category' => 'room-amenities', 'name' => 'Safe', 'icon' => 'fa-vault', 'sort_order' => 5],
            ['category' => 'room-amenities', 'name' => 'Work Desk', 'icon' => 'fa-desktop', 'sort_order' => 6],
            ['category' => 'room-amenities', 'name' => 'Wardrobe', 'icon' => 'fa-door-closed', 'sort_order' => 7],
            
            // Bathroom
            ['category' => 'bathroom', 'name' => 'Private Bathroom', 'icon' => 'fa-bath', 'sort_order' => 1],
            ['category' => 'bathroom', 'name' => 'Shower', 'icon' => 'fa-shower', 'sort_order' => 2],
            ['category' => 'bathroom', 'name' => 'Bathtub', 'icon' => 'fa-bath', 'sort_order' => 3],
            ['category' => 'bathroom', 'name' => 'Hair Dryer', 'icon' => 'fa-wind', 'sort_order' => 4],
            ['category' => 'bathroom', 'name' => 'Toiletries', 'icon' => 'fa-soap', 'sort_order' => 5],
            ['category' => 'bathroom', 'name' => 'Towels', 'icon' => 'fa-scroll', 'sort_order' => 6],
            
            // Media & Technology
            ['category' => 'media-technology', 'name' => 'Free WiFi', 'icon' => 'fa-wifi', 'sort_order' => 1, 'is_featured' => true],
            ['category' => 'media-technology', 'name' => 'Smart TV', 'icon' => 'fa-tv', 'sort_order' => 2],
            ['category' => 'media-technology', 'name' => 'Netflix', 'icon' => 'fa-film', 'sort_order' => 3],
            ['category' => 'media-technology', 'name' => 'USB Charging', 'icon' => 'fa-plug', 'sort_order' => 4],
            ['category' => 'media-technology', 'name' => 'Bluetooth Speaker', 'icon' => 'fa-volume-high', 'sort_order' => 5],
            
            // Food & Drink
            ['category' => 'food-drink', 'name' => 'Breakfast Included', 'icon' => 'fa-mug-saucer', 'sort_order' => 1, 'is_featured' => true],
            ['category' => 'food-drink', 'name' => 'Mini Bar', 'icon' => 'fa-wine-glass', 'sort_order' => 2],
            ['category' => 'food-drink', 'name' => 'Coffee Maker', 'icon' => 'fa-mug-hot', 'sort_order' => 3],
            ['category' => 'food-drink', 'name' => 'Electric Kettle', 'icon' => 'fa-mug-hot', 'sort_order' => 4],
            ['category' => 'food-drink', 'name' => 'Room Service', 'icon' => 'fa-bell-concierge', 'sort_order' => 5],
            ['category' => 'food-drink', 'name' => 'Kitchenette', 'icon' => 'fa-kitchen-set', 'sort_order' => 6],
            
            // Services
            ['category' => 'services', 'name' => 'Housekeeping', 'icon' => 'fa-broom', 'sort_order' => 1],
            ['category' => 'services', 'name' => 'Laundry Service', 'icon' => 'fa-shirt', 'sort_order' => 2],
            ['category' => 'services', 'name' => '24/7 Reception', 'icon' => 'fa-clock', 'sort_order' => 3],
            ['category' => 'services', 'name' => 'Concierge', 'icon' => 'fa-concierge-bell', 'sort_order' => 4],
            ['category' => 'services', 'name' => 'Airport Shuttle', 'icon' => 'fa-plane', 'sort_order' => 5, 'is_paid' => true],
            ['category' => 'services', 'name' => 'Car Rental', 'icon' => 'fa-car', 'sort_order' => 6, 'is_paid' => true],
            
            // Outdoor & View
            ['category' => 'outdoor-view', 'name' => 'Balcony', 'icon' => 'fa-square', 'sort_order' => 1],
            ['category' => 'outdoor-view', 'name' => 'Garden View', 'icon' => 'fa-tree', 'sort_order' => 2],
            ['category' => 'outdoor-view', 'name' => 'City View', 'icon' => 'fa-city', 'sort_order' => 3],
            ['category' => 'outdoor-view', 'name' => 'Sea View', 'icon' => 'fa-water', 'sort_order' => 4],
            ['category' => 'outdoor-view', 'name' => 'Mountain View', 'icon' => 'fa-mountain', 'sort_order' => 5],
            ['category' => 'outdoor-view', 'name' => 'Pool Access', 'icon' => 'fa-person-swimming', 'sort_order' => 6, 'is_featured' => true],
            
            // Accessibility
            ['category' => 'accessibility', 'name' => 'Wheelchair Accessible', 'icon' => 'fa-wheelchair', 'sort_order' => 1],
            ['category' => 'accessibility', 'name' => 'Elevator Access', 'icon' => 'fa-elevator', 'sort_order' => 2],
            ['category' => 'accessibility', 'name' => 'Ground Floor', 'icon' => 'fa-layer-group', 'sort_order' => 3],
            
            // Safety & Security
            ['category' => 'safety-security', 'name' => 'Fire Extinguisher', 'icon' => 'fa-fire-extinguisher', 'sort_order' => 1],
            ['category' => 'safety-security', 'name' => 'Smoke Detector', 'icon' => 'fa-bell', 'sort_order' => 2],
            ['category' => 'safety-security', 'name' => 'First Aid Kit', 'icon' => 'fa-kit-medical', 'sort_order' => 3],
            ['category' => 'safety-security', 'name' => 'CCTV', 'icon' => 'fa-video', 'sort_order' => 4],
            ['category' => 'safety-security', 'name' => 'Security Guard', 'icon' => 'fa-user-shield', 'sort_order' => 5],
            
            // Recreation
            ['category' => 'recreation', 'name' => 'Swimming Pool', 'icon' => 'fa-person-swimming', 'sort_order' => 1],
            ['category' => 'recreation', 'name' => 'Gym/Fitness', 'icon' => 'fa-dumbbell', 'sort_order' => 2],
            ['category' => 'recreation', 'name' => 'Spa', 'icon' => 'fa-spa', 'sort_order' => 3, 'is_paid' => true],
            ['category' => 'recreation', 'name' => 'Yoga Room', 'icon' => 'fa-peace', 'sort_order' => 4],
            ['category' => 'recreation', 'name' => 'Game Room', 'icon' => 'fa-gamepad', 'sort_order' => 5],
            
            // Business
            ['category' => 'business', 'name' => 'Business Center', 'icon' => 'fa-briefcase', 'sort_order' => 1],
            ['category' => 'business', 'name' => 'Meeting Room', 'icon' => 'fa-users', 'sort_order' => 2, 'is_paid' => true],
            ['category' => 'business', 'name' => 'Printer/Scanner', 'icon' => 'fa-print', 'sort_order' => 3],
        ];

        foreach ($amenities as $amenity) {
            $category = AmenityCategory::where('slug', $amenity['category'])->first();
            if ($category) {
                unset($amenity['category']);
                $amenity['category_id'] = $category->id;
                $amenity['slug'] = \Str::slug($amenity['name']);
                $amenity['is_active'] = true;
                $amenity['is_featured'] = $amenity['is_featured'] ?? false;
                $amenity['is_paid'] = $amenity['is_paid'] ?? false;
                
                self::updateOrCreate(['slug' => $amenity['slug']], $amenity);
            }
        }
    }
}
