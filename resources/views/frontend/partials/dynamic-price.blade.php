{{-- Dynamic Pricing Display Component --}}
{{-- Usage: @include('frontend.partials.dynamic-price', ['room' => $room, 'checkIn' => $checkIn, 'checkOut' => $checkOut]) --}}

@php
    $originalPrice = $room->price ?? 0;
    $checkIn = $checkIn ?? null;
    $checkOut = $checkOut ?? null;
    
    if ($checkIn && $checkOut) {
        $dynamicPrice = $room->getPriceForDates($checkIn, $checkOut);
        $hasDiscount = $dynamicPrice < $originalPrice;
        $discountPercent = $hasDiscount ? round((($originalPrice - $dynamicPrice) / $originalPrice) * 100) : 0;
    } else {
        $dynamicPrice = $originalPrice;
        $hasDiscount = false;
        $discountPercent = 0;
    }
    
    // Check for active pricing rules
    $activePricingRule = \App\Models\PricingRule::where('is_active', true)
        ->where(function($q) use ($checkIn) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $checkIn ?? now());
        })
        ->where(function($q) use ($checkOut) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $checkOut ?? now());
        })
        ->orderBy('priority', 'desc')
        ->first();
@endphp

<div class="price-display">
    @if($hasDiscount && $discountPercent > 0)
        {{-- Discounted Price Display --}}
        <div class="flex items-center gap-2">
            <span class="text-gray-400 line-through text-sm">₹{{ number_format($originalPrice) }}</span>
            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                {{ $discountPercent }}% OFF
            </span>
        </div>
        <div class="flex items-baseline mt-1">
            <span class="text-2xl font-bold text-green-600">₹{{ number_format($dynamicPrice) }}</span>
            <span class="text-gray-500 text-sm ml-1">/ night</span>
        </div>
        @if($activePricingRule)
            <div class="text-xs text-green-600 mt-1">
                <i class="fa-solid fa-tag mr-1"></i> {{ $activePricingRule->name }}
            </div>
        @endif
    @elseif($dynamicPrice > $originalPrice)
        {{-- Surge Pricing Display --}}
        <div class="flex items-center gap-2">
            <span class="text-gray-400 line-through text-sm">₹{{ number_format($originalPrice) }}</span>
            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full font-medium">
                High Demand
            </span>
        </div>
        <div class="flex items-baseline mt-1">
            <span class="text-2xl font-bold text-gray-900">₹{{ number_format($dynamicPrice) }}</span>
            <span class="text-gray-500 text-sm ml-1">/ night</span>
        </div>
        @if($activePricingRule)
            <div class="text-xs text-orange-600 mt-1">
                <i class="fa-solid fa-fire mr-1"></i> {{ $activePricingRule->name }}
            </div>
        @endif
    @else
        {{-- Regular Price Display --}}
        <div class="flex items-baseline">
            <span class="text-2xl font-bold text-gray-900">₹{{ number_format($originalPrice) }}</span>
            <span class="text-gray-500 text-sm ml-1">/ night</span>
        </div>
    @endif

    {{-- Tax Info --}}
    <p class="text-xs text-gray-400 mt-1">+ taxes & fees</p>
</div>
