<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'rules' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function cancellations()
    {
        return $this->hasMany(BookingCancellation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate refund percentage based on hours before check-in
     * Rules format: [
     *     ['hours' => 168, 'percentage' => 100], // 7+ days = 100% refund
     *     ['hours' => 48, 'percentage' => 50],   // 2-7 days = 50% refund
     *     ['hours' => 0, 'percentage' => 0],     // <2 days = no refund
     * ]
     */
    public function calculateRefundPercentage($hoursBeforeCheckIn)
    {
        $rules = collect($this->rules)->sortByDesc('hours');
        
        foreach ($rules as $rule) {
            if ($hoursBeforeCheckIn >= $rule['hours']) {
                return $rule['percentage'];
            }
        }
        
        return 0;
    }

    /**
     * Get human-readable policy description
     */
    public function getReadableRulesAttribute()
    {
        $descriptions = [];
        $rules = collect($this->rules)->sortByDesc('hours');
        
        foreach ($rules as $rule) {
            $hours = $rule['hours'];
            $percentage = $rule['percentage'];
            
            if ($hours >= 168) {
                $days = floor($hours / 24);
                $descriptions[] = "{$percentage}% refund if cancelled {$days}+ days before check-in";
            } elseif ($hours >= 24) {
                $days = floor($hours / 24);
                $descriptions[] = "{$percentage}% refund if cancelled {$days}+ days before check-in";
            } elseif ($hours > 0) {
                $descriptions[] = "{$percentage}% refund if cancelled {$hours}+ hours before check-in";
            } else {
                $descriptions[] = "{$percentage}% refund for last-minute cancellations";
            }
        }
        
        return $descriptions;
    }

    // Predefined policies
    public static function createDefaultPolicies()
    {
        $policies = [
            [
                'name' => 'Flexible',
                'slug' => 'flexible',
                'description' => 'Full refund up to 24 hours before check-in. After that, the first night is non-refundable.',
                'rules' => [
                    ['hours' => 24, 'percentage' => 100],
                    ['hours' => 0, 'percentage' => 0],
                ],
            ],
            [
                'name' => 'Moderate',
                'slug' => 'moderate',
                'description' => 'Full refund up to 5 days before check-in. 50% refund up to 24 hours before.',
                'rules' => [
                    ['hours' => 120, 'percentage' => 100],
                    ['hours' => 24, 'percentage' => 50],
                    ['hours' => 0, 'percentage' => 0],
                ],
            ],
            [
                'name' => 'Strict',
                'slug' => 'strict',
                'description' => 'Full refund up to 7 days before check-in. 50% refund up to 48 hours before.',
                'rules' => [
                    ['hours' => 168, 'percentage' => 100],
                    ['hours' => 48, 'percentage' => 50],
                    ['hours' => 0, 'percentage' => 0],
                ],
            ],
            [
                'name' => 'Non-Refundable',
                'slug' => 'non-refundable',
                'description' => 'This reservation is non-refundable. No refund will be issued for cancellations.',
                'rules' => [
                    ['hours' => 0, 'percentage' => 0],
                ],
            ],
        ];

        foreach ($policies as $policy) {
            self::updateOrCreate(['slug' => $policy['slug']], $policy);
        }
    }
}
