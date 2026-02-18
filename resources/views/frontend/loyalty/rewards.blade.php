@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('loyalty.index') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Loyalty Dashboard
            </a>
            <h1 class="text-3xl font-bold">Rewards Catalog</h1>
            <p class="text-gray-600 mt-1">Redeem your points for exclusive rewards</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-3 rounded-xl shadow-lg">
            <div class="text-sm opacity-90">Available Points</div>
            <div class="text-2xl font-bold">{{ number_format($userLoyalty->available_points ?? 0) }}</div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="{{ route('loyalty.rewards') }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All Rewards
        </a>
        <a href="{{ route('loyalty.rewards', ['category' => 'discount']) }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap {{ request('category') === 'discount' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-percent mr-1"></i> Discounts
        </a>
        <a href="{{ route('loyalty.rewards', ['category' => 'free_night']) }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap {{ request('category') === 'free_night' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-moon mr-1"></i> Free Nights
        </a>
        <a href="{{ route('loyalty.rewards', ['category' => 'upgrade']) }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap {{ request('category') === 'upgrade' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-arrow-up mr-1"></i> Upgrades
        </a>
        <a href="{{ route('loyalty.rewards', ['category' => 'experience']) }}" 
           class="px-4 py-2 rounded-full whitespace-nowrap {{ request('category') === 'experience' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fa-solid fa-star mr-1"></i> Experiences
        </a>
    </div>

    @if($rewards->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rewards as $reward)
                @php
                    $canRedeem = ($userLoyalty->available_points ?? 0) >= $reward->points_required;
                    $meetsMinTier = !$reward->min_tier_id || ($userLoyalty && $userLoyalty->tier->level >= $reward->minTier->level);
                @endphp
                
                <div class="bg-white rounded-xl shadow-md overflow-hidden {{ !$canRedeem || !$meetsMinTier ? 'opacity-75' : '' }}">
                    <!-- Reward Image -->
                    <div class="h-40 bg-gradient-to-br 
                        @switch($reward->type)
                            @case('discount')
                                from-blue-500 to-purple-600
                                @break
                            @case('free_night')
                                from-green-500 to-teal-600
                                @break
                            @case('upgrade')
                                from-yellow-500 to-orange-600
                                @break
                            @case('experience')
                                from-pink-500 to-red-600
                                @break
                            @default
                                from-gray-500 to-gray-600
                        @endswitch
                        relative flex items-center justify-center">
                        <i class="fa-solid 
                            @switch($reward->type)
                                @case('discount')
                                    fa-tag
                                    @break
                                @case('free_night')
                                    fa-moon
                                    @break
                                @case('upgrade')
                                    fa-crown
                                    @break
                                @case('experience')
                                    fa-spa
                                    @break
                                @default
                                    fa-gift
                            @endswitch
                            text-5xl text-white opacity-80"></i>
                        
                        @if($reward->min_tier_id)
                            <span class="absolute top-3 left-3 px-2 py-1 bg-black bg-opacity-50 text-white text-xs rounded-full">
                                {{ $reward->minTier->name }}+ Only
                            </span>
                        @endif

                        @if($reward->is_limited)
                            <span class="absolute top-3 right-3 px-2 py-1 bg-red-500 text-white text-xs rounded-full">
                                Limited
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-semibold text-lg mb-1">{{ $reward->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $reward->description }}</p>

                        <!-- Value Display -->
                        @if($reward->type === 'discount')
                            <div class="flex items-center text-sm text-green-600 mb-4">
                                <i class="fa-solid fa-percent mr-2"></i>
                                <span>{{ $reward->discount_percentage }}% off your next booking</span>
                            </div>
                        @elseif($reward->type === 'free_night')
                            <div class="flex items-center text-sm text-green-600 mb-4">
                                <i class="fa-solid fa-moon mr-2"></i>
                                <span>{{ $reward->free_nights }} free night(s)</span>
                            </div>
                        @endif

                        <!-- Points Required -->
                        <div class="flex items-center justify-between border-t pt-4">
                            <div>
                                <span class="text-2xl font-bold text-gray-900">{{ number_format($reward->points_required) }}</span>
                                <span class="text-gray-500 text-sm ml-1">points</span>
                            </div>

                            @if(!$meetsMinTier)
                                <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                    <i class="fa-solid fa-lock mr-1"></i> {{ $reward->minTier->name }} Required
                                </span>
                            @elseif(!$canRedeem)
                                <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                    Need {{ number_format($reward->points_required - ($userLoyalty->available_points ?? 0)) }} more
                                </span>
                            @else
                                <button onclick="redeemReward({{ $reward->id }}, '{{ $reward->name }}', {{ $reward->points_required }})" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    Redeem Now
                                </button>
                            @endif
                        </div>

                        <!-- Validity -->
                        @if($reward->valid_days)
                            <div class="mt-3 text-xs text-gray-400">
                                <i class="fa-solid fa-clock mr-1"></i> Valid for {{ $reward->valid_days }} days after redemption
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $rewards->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-gift text-4xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-semibold mb-2">No rewards available</h2>
            <p class="text-gray-600">Check back later for new rewards!</p>
        </div>
    @endif
</div>

<!-- Redemption Confirmation Modal -->
<div id="redeemModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-gift text-3xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-semibold">Confirm Redemption</h3>
        </div>

        <div class="text-center mb-6">
            <p class="text-gray-600 mb-4">You are about to redeem:</p>
            <p class="font-semibold text-lg" id="rewardName"></p>
            <p class="text-gray-500 mt-2">
                <span id="rewardPoints"></span> points will be deducted from your account
            </p>
        </div>

        <div class="flex gap-3">
            <button onclick="closeRedeemModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <form id="redeemForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirm Redemption
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function redeemReward(id, name, points) {
    document.getElementById('rewardName').textContent = name;
    document.getElementById('rewardPoints').textContent = points.toLocaleString();
    document.getElementById('redeemForm').action = `/loyalty/rewards/${id}/redeem`;
    document.getElementById('redeemModal').classList.remove('hidden');
    document.getElementById('redeemModal').classList.add('flex');
}

function closeRedeemModal() {
    document.getElementById('redeemModal').classList.add('hidden');
    document.getElementById('redeemModal').classList.remove('flex');
}
</script>
@endpush
@endsection
