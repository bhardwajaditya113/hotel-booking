<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->code = $booking->code ?? self::generateBookingCode();
        });
    }

    public function assign_rooms(){
        return $this->hasMany(BookingRoomList::class,'booking_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function room(){
        return $this->belongsTo(Room::class,'rooms_id','id');
    }

    // Property (hotel, home, etc.)
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // ==========================================
    // NEW RELATIONSHIPS FOR ENHANCED FEATURES
    // ==========================================

    // Review for this booking
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Cancellation record
    public function cancellation()
    {
        return $this->hasOne(BookingCancellation::class);
    }

    // Payment transactions
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Successful payments
    public function successfulPayments()
    {
        return $this->paymentTransactions()->successful();
    }

    // Wallet transactions
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Loyalty transactions
    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Coupon applied
    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 1);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 2);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 1)->where('check_out', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 1)->where('check_in', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 1)
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now());
    }

    public function scopeReviewable($query)
    {
        return $query->completed()
            ->whereDoesntHave('review');
    }

    // ==========================================
    // STATUS HELPERS
    // ==========================================

    public function isPending()
    {
        return $this->status == 0;
    }

    public function isConfirmed()
    {
        return $this->status == 1;
    }

    public function isCancelled()
    {
        return $this->status == 2;
    }

    public function isCompleted()
    {
        return $this->isConfirmed() && $this->check_out < now();
    }

    public function isUpcoming()
    {
        return $this->isConfirmed() && $this->check_in > now();
    }

    public function isOngoing()
    {
        return $this->isConfirmed() && $this->check_in <= now() && $this->check_out >= now();
    }

    public function canBeCancelled()
    {
        return $this->isConfirmed() && $this->check_in > now();
    }

    public function canBeReviewed()
    {
        return $this->isCompleted() && !$this->review;
    }

    // Get status label
    public function getStatusLabelAttribute()
    {
        return match((int)$this->status) {
            0 => 'Pending',
            1 => $this->isCompleted() ? 'Completed' : ($this->isOngoing() ? 'Ongoing' : 'Confirmed'),
            2 => 'Cancelled',
            default => 'Unknown',
        };
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        return match((int)$this->status) {
            0 => 'warning',
            1 => $this->isCompleted() ? 'success' : 'primary',
            2 => 'danger',
            default => 'secondary',
        };
    }

    // ==========================================
    // PRICING & PAYMENT METHODS
    // ==========================================

    // Get nights count
    public function getNightsAttribute()
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    // Get total paid
    public function getTotalPaidAttribute()
    {
        return $this->successfulPayments()->sum('amount');
    }

    // Get balance due
    public function getBalanceDueAttribute()
    {
        return max(0, $this->total_price - $this->total_paid);
    }

    // Check if fully paid
    public function isFullyPaid()
    {
        return $this->balance_due <= 0;
    }

    // Get formatted price
    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_price, 2);
    }

    // ==========================================
    // CANCELLATION METHODS
    // ==========================================

    // Calculate refund amount based on cancellation policy
    public function calculateRefundAmount()
    {
        $policy = $this->room?->cancellationPolicy ?? CancellationPolicy::getDefault();
        
        if (!$policy) {
            return 0;
        }

        return $policy->calculateRefund($this->total_price, $this->check_in);
    }

    // Process cancellation
    public function processCancellation($userId, $reason = null)
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('This booking cannot be cancelled');
        }

        $refundAmount = $this->calculateRefundAmount();
        $policy = $this->room?->cancellationPolicy ?? CancellationPolicy::getDefault();

        // Create cancellation record
        $cancellation = BookingCancellation::create([
            'booking_id' => $this->id,
            'user_id' => $this->user_id,
            'cancelled_by' => $userId,
            'cancellation_policy_id' => $policy?->id,
            'reason' => $reason,
            'refund_amount' => $refundAmount,
            'original_amount' => $this->total_price,
            'penalty_amount' => $this->total_price - $refundAmount,
            'status' => 'pending',
        ]);

        // Update booking status
        $this->update([
            'status' => 2,
            'cancelled_at' => now(),
        ]);

        // Release booked dates
        RoomBookedDate::whereHas('booking', function ($q) {
            $q->where('id', $this->id);
        })->delete();

        return $cancellation;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    // Generate unique booking code
    public static function generateBookingCode()
    {
        return 'BK-' . strtoupper(Str::random(4)) . '-' . date('YmdHis') . '-' . rand(100, 999);
    }

    // Award loyalty points
    public function awardLoyaltyPoints()
    {
        $userLoyalty = $this->user?->getOrCreateLoyalty();
        
        if ($userLoyalty && $userLoyalty->tier) {
            $points = floor($this->total_price * ($userLoyalty->tier->earning_rate / 100));
            
            if ($points > 0) {
                $userLoyalty->addPoints(
                    $points,
                    'booking',
                    "Points earned from booking #{$this->code}",
                    $this->id
                );

                // Send notification
                Notification::send(
                    $this->user_id,
                    'loyalty-points-earned',
                    [
                        'points' => $points,
                        'booking_code' => $this->code,
                        'total_points' => $userLoyalty->fresh()->current_points,
                    ],
                    $this->id
                );
            }
        }
    }

    // Send confirmation notification
    public function sendConfirmationNotification()
    {
        Notification::send(
            $this->user_id,
            'booking-confirmation',
            [
                'booking_code' => $this->code,
                'guest_name' => $this->name,
                'hotel_name' => config('app.name'),
                'check_in' => $this->check_in->format('M d, Y'),
                'check_out' => $this->check_out->format('M d, Y'),
                'room_name' => $this->room?->room_name ?? 'Room',
                'guests' => $this->number_of_guest ?? 1,
                'total_amount' => $this->formatted_total,
            ],
            $this->id
        );
    }
}
