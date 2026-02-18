@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Loyalty Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white mb-8">
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Loyalty Program</h1>
                <p class="text-blue-100">Earn points on every booking and unlock exclusive rewards</p>
            </div>
            <div class="text-right mt-4 lg:mt-0">
                <div class="text-4xl font-bold">{{ number_format($loyalty->current_points) }}</div>
                <div class="text-blue-100">Available Points</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Stats & Tier -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Current Tier Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl" 
                         style="background: {{ $loyalty->tier->color ?? '#CD7F32' }}20; color: {{ $loyalty->tier->color ?? '#CD7F32' }}">
                        <i class="{{ $loyalty->tier->icon ?? 'fa-solid fa-medal' }}"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold">{{ $loyalty->tier->name ?? 'Bronze' }}</h3>
                        <p class="text-gray-500">Current Tier</p>
                    </div>
                </div>
                
                @if($stats['next_tier'])
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Progress to {{ $stats['next_tier']->name }}</span>
                        <span>{{ number_format($stats['points_to_next_tier']) }} points needed</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        @php
                            $progress = min(100, ($loyalty->total_points / $stats['next_tier']->min_points) * 100);
                        @endphp
                        <div class="h-3 rounded-full" style="width: {{ $progress }}%; background: {{ $loyalty->tier->color ?? '#CD7F32' }}"></div>
                    </div>
                </div>
                @endif

                <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_earned']) }}</div>
                        <div class="text-xs text-gray-500">Total Earned</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_redeemed']) }}</div>
                        <div class="text-xs text-gray-500">Redeemed</div>
                    </div>
                </div>

                <a href="{{ route('loyalty.tiers') }}" class="block mt-4 text-center text-blue-600 hover:underline">
                    View All Tiers <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Tier Benefits -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Your Benefits</h3>
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                        <span>{{ $loyalty->tier->earning_rate ?? 5 }}% points on every booking</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                        <span>{{ $loyalty->tier->redemption_rate ?? 100 }} points = ₹1</span>
                    </li>
                    @if($loyalty->tier->benefits ?? false)
                        @foreach(json_decode($loyalty->tier->benefits, true) ?? [] as $benefit)
                        <li class="flex items-center">
                            <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                            <span>{{ $benefit }}</span>
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <!-- Referral Card -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fa-solid fa-user-plus mr-2 text-purple-500"></i>
                    Refer & Earn
                </h3>
                <p class="text-gray-600 text-sm mb-4">Invite friends and earn 500 bonus points when they complete their first booking!</p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <label class="text-xs text-gray-500 block mb-1">Your Referral Code</label>
                    <div class="flex items-center">
                        <input type="text" value="{{ $referralStats['code'] }}" readonly 
                               class="flex-1 bg-transparent font-mono text-lg font-bold" id="referralCode">
                        <button onclick="copyReferralCode()" class="ml-2 text-blue-600 hover:text-blue-800">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-center text-sm">
                    <div>
                        <div class="text-xl font-bold text-green-600">{{ $referralStats['successful_referrals'] }}</div>
                        <div class="text-gray-500">Successful</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-blue-600">{{ number_format($referralStats['total_earned']) }}</div>
                        <div class="text-gray-500">Points Earned</div>
                    </div>
                </div>

                <a href="{{ route('loyalty.referrals') }}" class="block mt-4 text-center text-purple-600 hover:underline">
                    View Referrals <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Right Column - Rewards & Transactions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('loyalty.rewards') }}" class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition">
                    <i class="fa-solid fa-gift text-2xl text-pink-500 mb-2"></i>
                    <div class="font-semibold">Rewards</div>
                </a>
                <a href="{{ route('loyalty.history') }}" class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition">
                    <i class="fa-solid fa-history text-2xl text-blue-500 mb-2"></i>
                    <div class="font-semibold">History</div>
                </a>
                <a href="{{ route('loyalty.my-rewards') }}" class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition">
                    <i class="fa-solid fa-ticket text-2xl text-green-500 mb-2"></i>
                    <div class="font-semibold">My Rewards</div>
                </a>
                <a href="{{ route('loyalty.tiers') }}" class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition">
                    <i class="fa-solid fa-layer-group text-2xl text-purple-500 mb-2"></i>
                    <div class="font-semibold">Tiers</div>
                </a>
            </div>

            <!-- Featured Rewards -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Featured Rewards</h3>
                    <a href="{{ route('loyalty.rewards') }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($rewards as $reward)
                    <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold">{{ $reward->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $reward->description }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">{{ number_format($reward->points_required) }}</div>
                                <div class="text-xs text-gray-400">points</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            @if($loyalty->current_points >= $reward->points_required)
                            <form action="{{ route('loyalty.rewards.redeem', $reward->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Redeem Now
                                </button>
                            </form>
                            @else
                            <button disabled class="w-full px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                                {{ number_format($reward->points_required - $loyalty->current_points) }} more points needed
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Recent Activity</h3>
                    <a href="{{ route('loyalty.history') }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                
                <div class="space-y-3">
                    @forelse($transactions as $transaction)
                    <div class="flex items-center justify-between py-3 border-b last:border-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'earned' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fa-solid {{ $transaction->type === 'earned' ? 'fa-plus' : 'fa-minus' }}"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium">{{ $transaction->description }}</div>
                                <div class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold {{ $transaction->type === 'earned' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'earned' ? '+' : '-' }}{{ number_format($transaction->points) }}
                            </div>
                            <div class="text-xs text-gray-400">points</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fa-solid fa-history text-4xl mb-2"></i>
                        <p>No transactions yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralCode() {
    const code = document.getElementById('referralCode');
    code.select();
    document.execCommand('copy');
    
    // Show toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    toast.textContent = 'Referral code copied!';
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 2000);
}
</script>
@endsection
