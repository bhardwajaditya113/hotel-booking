<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\LoyaltyReward;
use App\Models\CancellationPolicy;
use App\Models\Coupon;
use App\Models\PricingRule;
use Illuminate\Http\Request;

class EnhancedFeaturesController extends Controller
{
    // ==========================================
    // REVIEW MANAGEMENT
    // ==========================================
    
    /**
     * List all reviews
     */
    public function reviewIndex(Request $request)
    {
        $query = Review::with(['user', 'room.property', 'room.type', 'booking']);
        
        // Filter by status
        if ($request->status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($request->status === 'rejected') {
            $query->where('is_approved', false)->whereNotNull('rejection_reason');
        } else {
            $query->where('is_approved', false)->whereNull('rejection_reason');
        }
        
        $reviews = $query->latest()->paginate(20);
        
        $pendingCount = Review::where('is_approved', false)->whereNull('rejection_reason')->count();
        $approvedCount = Review::where('is_approved', true)->count();
        
        return view('backend.reviews.index', compact('reviews', 'pendingCount', 'approvedCount'));
    }

    /**
     * Show review details
     */
    public function reviewShow($id)
    {
        $review = Review::with(['user', 'room', 'booking', 'photos'])->findOrFail($id);
        return view('backend.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function reviewApprove($id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'is_approved' => true,
        ]);
        
        // Update property rating stats
        if ($review->room && $review->room->property) {
            $review->room->property->updateRatingStats();
        }
        
        return back()->with('success', 'Review approved successfully');
    }

    /**
     * Reject a review
     */
    public function reviewReject(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'is_approved' => false,
            'rejection_reason' => $request->reason,
        ]);
        
        return back()->with('success', 'Review rejected');
    }

    /**
     * Respond to a review
     */
    public function reviewRespond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:1000',
        ]);
        
        $review = Review::findOrFail($id);
        $review->update([
            'manager_response' => $request->response,
            'manager_response_at' => now(),
            'responded_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Response added successfully');
    }

    // ==========================================
    // LOYALTY REWARDS MANAGEMENT
    // ==========================================
    
    /**
     * List all rewards
     */
    public function rewardIndex()
    {
        $rewards = LoyaltyReward::with('minTier')->latest()->paginate(20);
        return view('backend.loyalty.rewards.index', compact('rewards'));
    }

    /**
     * Create reward form
     */
    public function rewardCreate()
    {
        $tiers = \App\Models\LoyaltyTier::orderBy('level')->get();
        return view('backend.loyalty.rewards.create', compact('tiers'));
    }

    /**
     * Store new reward
     */
    public function rewardStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:discount,free_night,upgrade,amenity,experience',
            'points_required' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'free_nights' => 'nullable|integer|min:1',
            'min_tier_id' => 'nullable|exists:loyalty_tiers,id',
            'valid_days' => 'nullable|integer|min:1',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        LoyaltyReward::create($validated);
        
        return redirect()->route('admin.loyalty.rewards.index')
            ->with('success', 'Reward created successfully');
    }

    /**
     * Update reward
     */
    public function rewardUpdate(Request $request, $id)
    {
        $reward = LoyaltyReward::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_required' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'free_nights' => 'nullable|integer|min:1',
            'min_tier_id' => 'nullable|exists:loyalty_tiers,id',
            'is_active' => 'boolean',
        ]);
        
        $reward->update($validated);
        
        return back()->with('success', 'Reward updated successfully');
    }

    /**
     * Edit reward form
     */
    public function rewardEdit($id)
    {
        $reward = LoyaltyReward::findOrFail($id);
        $tiers = \App\Models\LoyaltyTier::orderBy('level')->get();
        return view('backend.loyalty.rewards.edit', compact('reward', 'tiers'));
    }

    /**
     * Delete reward
     */
    public function rewardDestroy($id)
    {
        $reward = LoyaltyReward::findOrFail($id);
        $reward->delete();
        
        return back()->with('success', 'Reward deleted successfully');
    }

    // ==========================================
    // CANCELLATION POLICY MANAGEMENT
    // ==========================================
    
    /**
     * List all policies
     */
    public function policyIndex()
    {
        $policies = CancellationPolicy::withCount('rooms')->get();
        return view('backend.policies.index', compact('policies'));
    }

    /**
     * Create policy form
     */
    public function policyCreate()
    {
        return view('backend.policies.create');
    }

    /**
     * Store new policy
     */
    public function policyStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:cancellation_policies,slug',
            'description' => 'nullable|string',
            'days_before_checkin' => 'required|integer|min:0',
            'full_refund_percentage' => 'required|numeric|min:0|max:100',
            'partial_refund_percentage' => 'required|numeric|min:0|max:100',
            'days_before_partial_refund' => 'required|integer|min:0',
            'hours_before_full_charge' => 'required|integer|min:0',
            'is_free_cancellation' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        // If setting as default, unset others
        if ($request->is_default) {
            CancellationPolicy::where('is_default', true)->update(['is_default' => false]);
        }
        
        CancellationPolicy::create($validated);
        
        return redirect()->route('admin.policies.index')
            ->with('success', 'Cancellation policy created successfully');
    }

    /**
     * Edit policy form
     */
    public function policyEdit($id)
    {
        $policy = CancellationPolicy::findOrFail($id);
        return view('backend.policies.edit', compact('policy'));
    }

    /**
     * Update policy
     */
    public function policyUpdate(Request $request, $id)
    {
        $policy = CancellationPolicy::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:cancellation_policies,slug,' . $id,
            'description' => 'nullable|string',
            'days_before_checkin' => 'required|integer|min:0',
            'full_refund_percentage' => 'required|numeric|min:0|max:100',
            'partial_refund_percentage' => 'required|numeric|min:0|max:100',
            'days_before_partial_refund' => 'required|integer|min:0',
            'hours_before_full_charge' => 'required|integer|min:0',
            'is_free_cancellation' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        // If setting as default, unset others
        if ($request->is_default) {
            CancellationPolicy::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }
        
        $policy->update($validated);
        
        return redirect()->route('admin.policies.index')
            ->with('success', 'Cancellation policy updated successfully');
    }

    /**
     * Delete policy
     */
    public function policyDestroy($id)
    {
        $policy = CancellationPolicy::findOrFail($id);
        $policy->delete();
        
        return back()->with('success', 'Cancellation policy deleted successfully');
    }

    // ==========================================
    // COUPON MANAGEMENT
    // ==========================================
    
    /**
     * List all coupons
     */
    public function couponIndex()
    {
        $coupons = Coupon::withCount('usages')
            ->latest()
            ->paginate(20);
        return view('backend.coupons.index', compact('coupons'));
    }

    /**
     * Create coupon form
     */
    public function couponCreate()
    {
        return view('backend.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function couponStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);
        
        $validated['code'] = strtoupper($validated['code']);
        
        Coupon::create($validated);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully');
    }

    /**
     * Edit coupon form
     */
    public function couponEdit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('backend.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function couponUpdate(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $id . '|max:50',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);
        
        $validated['code'] = strtoupper($validated['code']);
        $coupon->update($validated);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully');
    }

    /**
     * Toggle coupon active status
     */
    public function couponToggle($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);
        
        return back()->with('success', 'Coupon status updated');
    }

    /**
     * Delete coupon
     */
    public function couponDestroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        
        return back()->with('success', 'Coupon deleted successfully');
    }

    // ==========================================
    // PRICING RULES MANAGEMENT
    // ==========================================
    
    /**
     * List all pricing rules
     */
    public function pricingIndex()
    {
        $rules = PricingRule::latest()->paginate(20);
        return view('backend.pricing.index', compact('rules'));
    }

    /**
     * Create pricing rule form
     */
    public function pricingCreate()
    {
        return view('backend.pricing.create');
    }

    /**
     * Store new pricing rule
     */
    public function pricingStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:weekend,seasonal,last_minute,early_bird,event,occupancy',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'days_of_week' => 'nullable|array',
            'min_days_before' => 'nullable|integer|min:0',
            'max_days_before' => 'nullable|integer|min:0',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        PricingRule::create($validated);
        
        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing rule created successfully');
    }

    /**
     * Edit pricing rule form
     */
    public function pricingEdit($id)
    {
        $rule = PricingRule::findOrFail($id);
        return view('backend.pricing.edit', compact('rule'));
    }

    /**
     * Update pricing rule
     */
    public function pricingUpdate(Request $request, $id)
    {
        $rule = PricingRule::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:weekend,seasonal,last_minute,early_bird,event,occupancy',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'days_of_week' => 'nullable|array',
            'min_days_before' => 'nullable|integer|min:0',
            'max_days_before' => 'nullable|integer|min:0',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $rule->update($validated);
        
        return redirect()->route('admin.pricing.index')
            ->with('success', 'Pricing rule updated successfully');
    }

    /**
     * Toggle pricing rule active status
     */
    public function pricingToggle($id)
    {
        $rule = PricingRule::findOrFail($id);
        $rule->update(['is_active' => !$rule->is_active]);
        
        return back()->with('success', 'Pricing rule status updated');
    }

    /**
     * Delete pricing rule
     */
    public function pricingDestroy($id)
    {
        $rule = PricingRule::findOrFail($id);
        $rule->delete();
        
        return back()->with('success', 'Pricing rule deleted successfully');
    }

    // ==========================================
    // DASHBOARD STATS
    // ==========================================
    
    /**
     * Get enhanced features stats for dashboard
     */
    public function dashboardStats()
    {
        return [
            'pending_reviews' => Review::where('is_approved', false)->whereNull('rejection_reason')->count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'total_loyalty_points' => \App\Models\UserLoyalty::sum('total_points') ?? 0,
            'wallet_balance' => \App\Models\Wallet::sum('balance') ?? 0,
        ];
    }
}
