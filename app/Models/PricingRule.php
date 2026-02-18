<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'days_of_week' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_stackable' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId)
            ->orWhereNull('room_id');
    }

    public function scopeForRoomType($query, $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId)
            ->orWhereNull('room_type_id');
    }

    public function scopeApplicableOn($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $date);
        });
    }

    // Check if rule applies to a specific date
    public function appliesTo($date)
    {
        $date = \Carbon\Carbon::parse($date);

        // Check date range
        if ($this->start_date && $date->lt($this->start_date)) return false;
        if ($this->end_date && $date->gt($this->end_date)) return false;

        // Check day of week
        if ($this->days_of_week && !in_array($date->dayOfWeek, $this->days_of_week)) {
            return false;
        }

        return true;
    }

    // Calculate adjustment for a base price
    public function calculateAdjustment($basePrice)
    {
        if ($this->adjustment_type === 'percentage') {
            return $basePrice * ($this->adjustment_value / 100);
        }
        return $this->adjustment_value;
    }

    // Apply adjustment to price
    public function applyTo($basePrice)
    {
        $adjustment = $this->calculateAdjustment($basePrice);
        return $basePrice + $adjustment;
    }

    // Get display text for the rule
    public function getDisplayTextAttribute()
    {
        $sign = $this->adjustment_value >= 0 ? '+' : '';
        $value = $this->adjustment_type === 'percentage' 
            ? "{$sign}{$this->adjustment_value}%"
            : "{$sign}₹{$this->adjustment_value}";
        
        return "{$this->name}: {$value}";
    }

    // Get applicable rules for room and date
    public static function getApplicableRules($roomId, $roomTypeId, $date, $daysAdvance = null, $nights = null)
    {
        $query = self::active()
            ->where(function ($q) use ($roomId, $roomTypeId) {
                $q->where('room_id', $roomId)
                  ->orWhere('room_type_id', $roomTypeId)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('room_id')->whereNull('room_type_id');
                  });
            })
            ->applicableOn($date)
            ->orderBy('priority');

        $rules = $query->get()->filter(function ($rule) use ($date, $daysAdvance, $nights) {
            // Check day of week
            if (!$rule->appliesTo($date)) return false;

            // Check advance booking
            if ($rule->min_days_advance !== null && $daysAdvance < $rule->min_days_advance) return false;
            if ($rule->max_days_advance !== null && $daysAdvance > $rule->max_days_advance) return false;

            // Check length of stay
            if ($rule->min_nights !== null && $nights < $rule->min_nights) return false;
            if ($rule->max_nights !== null && $nights > $rule->max_nights) return false;

            return true;
        });

        return $rules;
    }

    // Calculate final price with all applicable rules
    public static function calculatePrice($basePrice, $roomId, $roomTypeId, $date, $daysAdvance = null, $nights = null)
    {
        $rules = self::getApplicableRules($roomId, $roomTypeId, $date, $daysAdvance, $nights);
        
        $price = $basePrice;
        $appliedRules = [];

        // First, apply non-stackable rules (only the highest priority one)
        $nonStackable = $rules->where('is_stackable', false)->first();
        if ($nonStackable) {
            $price = $nonStackable->applyTo($price);
            $appliedRules[] = $nonStackable;
        }

        // Then apply all stackable rules
        foreach ($rules->where('is_stackable', true) as $rule) {
            $price = $rule->applyTo($price);
            $appliedRules[] = $rule;
        }

        return [
            'base_price' => $basePrice,
            'final_price' => max(0, round($price, 2)),
            'applied_rules' => $appliedRules,
        ];
    }
}
