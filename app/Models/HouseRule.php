<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_house_rules')
            ->withPivot('custom_value', 'is_allowed')
            ->withTimestamps();
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

    // Create default house rules
    public static function createDefaults()
    {
        $rules = [
            [
                'name' => 'Check-in Time',
                'slug' => 'check-in-time',
                'icon' => 'fa-clock',
                'default_value' => '14:00',
                'type' => 'time',
                'sort_order' => 1,
            ],
            [
                'name' => 'Check-out Time',
                'slug' => 'check-out-time',
                'icon' => 'fa-clock',
                'default_value' => '11:00',
                'type' => 'time',
                'sort_order' => 2,
            ],
            [
                'name' => 'Smoking',
                'slug' => 'smoking',
                'icon' => 'fa-smoking',
                'default_value' => 'Not Allowed',
                'type' => 'boolean',
                'sort_order' => 3,
            ],
            [
                'name' => 'Pets',
                'slug' => 'pets',
                'icon' => 'fa-paw',
                'default_value' => 'Not Allowed',
                'type' => 'boolean',
                'sort_order' => 4,
            ],
            [
                'name' => 'Parties/Events',
                'slug' => 'parties-events',
                'icon' => 'fa-champagne-glasses',
                'default_value' => 'Not Allowed',
                'type' => 'boolean',
                'sort_order' => 5,
            ],
            [
                'name' => 'Quiet Hours',
                'slug' => 'quiet-hours',
                'icon' => 'fa-moon',
                'default_value' => '10:00 PM - 8:00 AM',
                'type' => 'text',
                'sort_order' => 6,
            ],
            [
                'name' => 'Minimum Age',
                'slug' => 'minimum-age',
                'icon' => 'fa-user',
                'default_value' => '18',
                'type' => 'number',
                'sort_order' => 7,
            ],
            [
                'name' => 'ID Required',
                'slug' => 'id-required',
                'icon' => 'fa-id-card',
                'default_value' => 'Government ID required at check-in',
                'type' => 'text',
                'sort_order' => 8,
            ],
            [
                'name' => 'Maximum Guests',
                'slug' => 'maximum-guests',
                'icon' => 'fa-users',
                'default_value' => 'As per room capacity',
                'type' => 'text',
                'sort_order' => 9,
            ],
            [
                'name' => 'Children',
                'slug' => 'children',
                'icon' => 'fa-child',
                'default_value' => 'Allowed',
                'type' => 'boolean',
                'sort_order' => 10,
            ],
            [
                'name' => 'Extra Beds',
                'slug' => 'extra-beds',
                'icon' => 'fa-bed',
                'default_value' => 'Available on request',
                'type' => 'text',
                'sort_order' => 11,
            ],
            [
                'name' => 'Damage Deposit',
                'slug' => 'damage-deposit',
                'icon' => 'fa-shield',
                'default_value' => 'May be required',
                'type' => 'text',
                'sort_order' => 12,
            ],
        ];

        foreach ($rules as $rule) {
            $rule['is_active'] = true;
            self::updateOrCreate(['slug' => $rule['slug']], $rule);
        }
    }
}
