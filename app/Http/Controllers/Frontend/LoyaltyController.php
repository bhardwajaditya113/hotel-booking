<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserLoyalty;
use App\Models\LoyaltyTier;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyReward;
use App\Models\UserReward;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    /**
     * Display loyalty dashboard
     */
    public function index()
    {
        $loyalty = Auth::user()->getOrCreateLoyalty();
        $loyalty->load('tier');
        
        $tiers = LoyaltyTier::active()->ordered()->get();
        
        $transactions = $loyalty->transactions()
            ->latest()
            ->limit(10)
            ->get();
        
        $rewards = LoyaltyReward::active()
            ->available()
            ->ordered()
            ->limit(6)
            ->get();
        
        $stats = [
            'total_earned' => $loyalty->total_points,
            'current_balance' => $loyalty->current_points,
            'total_redeemed' => $loyalty->total_points - $loyalty->current_points,
            'points_to_next_tier' => $loyalty->getPointsToNextTier(),
            'next_tier' => $loyalty->getNextTier(),
        ];
        
        // Referral stats
        $referralStats = [
            'code' => Auth::user()->generateReferralCode(),
            'successful_referrals' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'completed')
                ->count(),
            'pending_referrals' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'total_earned' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'completed')
                ->sum('referrer_bonus'),
        ];
        
        return view('frontend.loyalty.index', compact(
            'loyalty', 'tiers', 'transactions', 'rewards', 'stats', 'referralStats'
        ));
    }

    /**
     * View all tiers
     */
    public function tiers()
    {
        $tiers = LoyaltyTier::active()->ordered()->get();
        $userLoyalty = Auth::user()->getOrCreateLoyalty();
        
        return view('frontend.loyalty.tiers', compact('tiers', 'userLoyalty'));
    }

    /**
     * View points history
     */
    public function history(Request $request)
    {
        $loyalty = Auth::user()->loyalty;
        
        if (!$loyalty) {
            return redirect()->route('loyalty.index');
        }
        
        $query = $loyalty->transactions();
        
        // Filter by type
        if ($request->type === 'earned') {
            $query->earned();
        } elseif ($request->type === 'redeemed') {
            $query->redeemed();
        }
        
        // Filter by date
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('frontend.loyalty.history', compact('transactions'));
    }

    /**
     * View rewards catalog
     */
    public function rewards()
    {
        $loyalty = Auth::user()->getOrCreateLoyalty();
        
        $rewards = LoyaltyReward::active()
            ->available()
            ->ordered()
            ->paginate(12);
        
        return view('frontend.loyalty.rewards', compact('rewards', 'loyalty'));
    }

    /**
     * Redeem a reward
     */
    public function redeemReward(Request $request, $rewardId)
    {
        $reward = LoyaltyReward::active()->findOrFail($rewardId);
        $loyalty = Auth::user()->getOrCreateLoyalty();
        
        // Check if user can redeem
        if (!$reward->canBeRedeemed($loyalty)) {
            return back()->with('error', 'You cannot redeem this reward');
        }
        
        // Check points balance
        if ($loyalty->current_points < $reward->points_required) {
            return back()->with('error', 'Insufficient points');
        }
        
        // Check tier requirement
        if ($reward->min_tier_id && $loyalty->tier_id < $reward->min_tier_id) {
            return back()->with('error', 'Your tier level is not eligible for this reward');
        }
        
        // Check stock
        if ($reward->stock !== null && $reward->stock <= 0) {
            return back()->with('error', 'This reward is out of stock');
        }
        
        // Create user reward
        $userReward = UserReward::create([
            'user_id' => Auth::id(),
            'reward_id' => $reward->id,
            'points_spent' => $reward->points_required,
            'status' => $reward->type === 'instant' ? 'used' : 'active',
            'expires_at' => $reward->validity_days 
                ? now()->addDays($reward->validity_days) 
                : null,
            'value' => $reward->value,
            'value_type' => $reward->value_type,
        ]);
        
        // Deduct points
        $loyalty->redeemPoints(
            $reward->points_required,
            'reward_redemption',
            "Redeemed: {$reward->name}",
            null,
            $userReward->id
        );
        
        // Reduce stock if applicable
        if ($reward->stock !== null) {
            $reward->decrement('stock');
        }
        
        // Apply instant rewards
        if ($reward->type === 'instant') {
            $this->applyInstantReward($userReward);
        }
        
        return back()->with('success', 'Reward redeemed successfully!');
    }

    /**
     * Apply instant reward
     */
    protected function applyInstantReward($userReward)
    {
        $reward = $userReward->reward;
        
        switch ($reward->value_type) {
            case 'wallet_credit':
                $wallet = Auth::user()->getOrCreateWallet();
                $wallet->credit(
                    $reward->value,
                    'reward',
                    "Loyalty reward: {$reward->name}",
                    null,
                    $reward->validity_days ? now()->addDays($reward->validity_days) : null
                );
                break;
            // Add other instant reward types as needed
        }
        
        $userReward->update(['used_at' => now()]);
    }

    /**
     * View user's redeemed rewards
     */
    public function myRewards()
    {
        $rewards = UserReward::where('user_id', Auth::id())
            ->with('reward')
            ->latest()
            ->paginate(12);
        
        return view('frontend.loyalty.my-rewards', compact('rewards'));
    }

    /**
     * Use a reward (get coupon code, etc.)
     */
    public function useReward($userRewardId)
    {
        $userReward = UserReward::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($userRewardId);
        
        // Check expiry
        if ($userReward->expires_at && $userReward->expires_at < now()) {
            $userReward->update(['status' => 'expired']);
            return back()->with('error', 'This reward has expired');
        }
        
        return view('frontend.loyalty.use-reward', compact('userReward'));
    }

    /**
     * Referral page
     */
    public function referrals()
    {
        $referralCode = Auth::user()->generateReferralCode();
        
        $referrals = Referral::where('referrer_id', Auth::id())
            ->with('referred')
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => $referrals->total(),
            'completed' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'completed')
                ->count(),
            'pending' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'pending')
                ->count(),
            'total_earned' => Referral::where('referrer_id', Auth::id())
                ->where('status', 'completed')
                ->sum('referrer_bonus'),
        ];
        
        $referralUrl = url('/register?ref=' . $referralCode);
        
        return view('frontend.loyalty.referrals', compact('referrals', 'stats', 'referralCode', 'referralUrl'));
    }

    /**
     * Process referral signup
     */
    public static function processReferralSignup($newUser, $referralCode)
    {
        if (!$referralCode) return;
        
        $referrer = User::where('referral_code', $referralCode)->first();
        
        if (!$referrer || $referrer->id === $newUser->id) return;
        
        // Check if already referred
        $existing = Referral::where('referred_id', $newUser->id)->exists();
        if ($existing) return;
        
        // Get referral bonuses from config or defaults
        $referrerBonus = config('loyalty.referral_bonus', 500);
        $referredBonus = config('loyalty.referred_bonus', 250);
        
        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_code' => $referralCode,
            'status' => 'pending', // Will complete after first booking
            'referrer_bonus' => $referrerBonus,
            'referred_bonus' => $referredBonus,
        ]);
    }

    /**
     * Complete referral after first booking
     */
    public static function completeReferral($userId)
    {
        $referral = Referral::where('referred_id', $userId)
            ->where('status', 'pending')
            ->first();
        
        if (!$referral) return;
        
        // Award bonuses
        $referrerLoyalty = User::find($referral->referrer_id)?->getOrCreateLoyalty();
        $referredLoyalty = User::find($referral->referred_id)?->getOrCreateLoyalty();
        
        if ($referrerLoyalty) {
            $referrerLoyalty->addPoints(
                $referral->referrer_bonus,
                'referral',
                'Referral bonus for inviting a friend',
                null,
                $referral->id
            );
        }
        
        if ($referredLoyalty) {
            $referredLoyalty->addPoints(
                $referral->referred_bonus,
                'signup_bonus',
                'Welcome bonus for joining via referral',
                null,
                $referral->id
            );
        }
        
        $referral->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get points balance via AJAX
     */
    public function getBalance()
    {
        $loyalty = Auth::user()->loyalty;
        
        return response()->json([
            'current_points' => $loyalty?->current_points ?? 0,
            'tier' => $loyalty?->tier?->name ?? 'Bronze',
        ]);
    }
}
