<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use DB;

class User extends Authenticatable
{
    // Host profile (for individual or company hosts)
    public function hostProfile()
    {
        return $this->hasOne(HostProfile::class);
    }

    // Properties owned/hosted by this user

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getpermissionGroups(){

        $permission_groups = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $permission_groups;

    } // End Method 

    public static function getpermissionByGroupName($group_name){

        $permissions = DB::table('permissions')
                            ->select('name','id')
                            ->where('group_name',$group_name)
                            ->get();
            return $permissions;

    }// End Method 

    public static function roleHasPermissions($role,$permissions){
        $hasPermission = true;
        foreach ($permissions as $permission) {
           if (!$role->hasPermissionTo($permission->name)) {
            $hasPermission = false;
           }
           return $hasPermission;
        }
    }// End Method 

    // ==========================================
    // NEW RELATIONSHIPS FOR ENHANCED FEATURES
    // ==========================================

    // Bookings relationship
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Reviews written by user
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Review helpful votes
    public function reviewVotes()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    // Wishlists
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Default wishlist
    public function defaultWishlist()
    {
        return $this->hasOne(Wishlist::class)->where('is_default', true);
    }

    // Wallet
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Wallet transactions
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Loyalty membership
    public function loyalty()
    {
        return $this->hasOne(UserLoyalty::class);
    }

    // Loyalty transactions
    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // Rewards earned
    public function rewards()
    {
        return $this->hasMany(UserReward::class);
    }

    // Referrals made
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    // Referred by
    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    // Saved payment methods
    public function paymentMethods()
    {
        return $this->hasMany(UserPaymentMethod::class);
    }

    // Payment transactions
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Coupon usages
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    // Get or create wallet
    public function getOrCreateWallet()
    {
        return Wallet::getOrCreate($this->id);
    }

    // Get or create loyalty
    public function getOrCreateLoyalty()
    {
        return UserLoyalty::firstOrCreate(
            ['user_id' => $this->id],
            ['tier_id' => LoyaltyTier::getDefault()?->id, 'total_points' => 0, 'current_points' => 0]
        );
    }

    // Get or create default wishlist
    public function getOrCreateDefaultWishlist()
    {
        return Wishlist::firstOrCreate(
            ['user_id' => $this->id, 'is_default' => true],
            ['name' => 'My Favorites']
        );
    }

    // Get unread notifications count
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->unread()->count();
    }

    // Get loyalty tier
    public function getLoyaltyTierAttribute()
    {
        return $this->loyalty?->tier;
    }

    // Get wallet balance
    public function getWalletBalanceAttribute()
    {
        return $this->wallet?->balance ?? 0;
    }

    // Check if user can review a booking
    public function canReviewBooking(Booking $booking)
    {
        // User must be the booking owner and booking must be completed
        if ($booking->user_id !== $this->id) return false;
        if ($booking->status !== 1) return false; // 1 = completed
        if ($booking->check_out > now()) return false;
        
        // Check if already reviewed
        return !Review::where('booking_id', $booking->id)
            ->where('user_id', $this->id)
            ->exists();
    }

    // Check if room is in wishlist
    public function hasInWishlist($roomId)
    {
        return $this->wishlists()
            ->whereHas('items', function ($q) use ($roomId) {
                $q->where('room_id', $roomId);
            })
            ->exists();
    }

    // Generate referral code
    public function generateReferralCode()
    {
        if (!$this->referral_code) {
            $code = strtoupper(substr($this->name, 0, 3)) . $this->id . strtoupper(\Str::random(4));
            $this->update(['referral_code' => $code]);
        }
        return $this->referral_code;
    }

    // ==========================================
    // MESSAGING RELATIONSHIPS
    // ==========================================

    // Conversations where user is participant 1
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    // Conversations where user is participant 2
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    // All conversations
    public function conversations()
    {
        return Conversation::where(function($q) {
            $q->where('user1_id', $this->id)
              ->orWhere('user2_id', $this->id);
        });
    }

    // Messages sent by user
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Messages received by user
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Unread messages count
    public function getUnreadMessagesCountAttribute()
    {
        return $this->receivedMessages()->unread()->count();
    }

    // ==========================================
    // HOST/COMPANY HELPERS
    // ==========================================

    public function isHost()
    {
        return $this->properties()->exists() || $this->hostProfile()->exists();
    }

    public function isCompanyHost()
    {
        return $this->hostProfile && $this->hostProfile->isCompany();
    }

    public function isIndividualHost()
    {
        return $this->hostProfile && $this->hostProfile->isIndividual();
    }
}
