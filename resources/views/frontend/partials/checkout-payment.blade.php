{{-- Enhanced Payment Options for Checkout --}}
{{-- Include in checkout page: @include('frontend.partials.checkout-payment', ['booking' => $booking]) --}}

<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-semibold mb-6">Payment Options</h3>

    {{-- Coupon Code --}}
    <div class="mb-6 pb-6 border-b">
        <label class="block text-sm font-medium text-gray-700 mb-2">Have a coupon code?</label>
        <div class="flex gap-2">
            <input type="text" id="coupon_code" name="coupon_code" 
                   placeholder="Enter coupon code"
                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="button" onclick="applyCoupon()" 
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Apply
            </button>
        </div>
        <div id="coupon-status" class="mt-2 text-sm hidden"></div>
        <input type="hidden" name="applied_coupon_id" id="applied_coupon_id">
    </div>

    {{-- Loyalty Points --}}
    @auth
        @php
            $userLoyalty = Auth::user()->loyalty;
            $availablePoints = $userLoyalty ? $userLoyalty->available_points : 0;
            $pointsValue = $availablePoints * 0.25; // 1 point = ₹0.25
            $maxPointsToUse = min($availablePoints, ($booking->total_amount ?? 0) * 0.20 / 0.25); // Max 20% of total
        @endphp
        
        @if($availablePoints > 0)
            <div class="mb-6 pb-6 border-b">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700">Use Loyalty Points</label>
                    <span class="text-sm text-gray-500">
                        Available: <strong class="text-blue-600">{{ number_format($availablePoints) }}</strong> points
                    </span>
                </div>
                
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-4 mb-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="use_loyalty_points" id="use_loyalty_points" 
                               class="w-5 h-5 text-blue-600 rounded" 
                               onchange="toggleLoyaltyPoints()">
                        <span class="ml-3">
                            <span class="font-medium">Redeem points for discount</span>
                            <span class="block text-sm text-gray-500">Up to ₹{{ number_format($maxPointsToUse * 0.25) }} off this booking</span>
                        </span>
                    </label>
                </div>

                <div id="loyalty-points-slider" class="hidden mt-4">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span>0 points</span>
                        <span>{{ number_format($maxPointsToUse) }} points</span>
                    </div>
                    <input type="range" name="loyalty_points_to_use" id="loyalty_points_range"
                           min="0" max="{{ $maxPointsToUse }}" value="0"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateLoyaltyPointsValue()">
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm text-gray-500">
                            Using: <strong id="points-using">0</strong> points
                        </span>
                        <span class="text-sm text-green-600 font-medium">
                            Save: ₹<span id="points-saving">0</span>
                        </span>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- Wallet Payment --}}
    @auth
        @php
            $wallet = Auth::user()->wallet;
            $walletBalance = $wallet ? $wallet->balance : 0;
        @endphp
        
        @if($walletBalance > 0)
            <div class="mb-6 pb-6 border-b">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700">Pay with Wallet</label>
                    <span class="text-sm text-gray-500">
                        Balance: <strong class="text-green-600">₹{{ number_format($walletBalance, 2) }}</strong>
                    </span>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="use_wallet" id="use_wallet" 
                               class="w-5 h-5 text-green-600 rounded"
                               data-balance="{{ $walletBalance }}"
                               onchange="toggleWalletPayment()">
                        <span class="ml-3">
                            <span class="font-medium">Use wallet balance</span>
                            <span class="block text-sm text-gray-500">
                                @if($walletBalance >= ($booking->total_amount ?? 0))
                                    Full payment from wallet
                                @else
                                    Partial payment (₹{{ number_format($walletBalance, 2) }})
                                @endif
                            </span>
                        </span>
                    </label>
                </div>
                <input type="hidden" name="wallet_amount" id="wallet_amount" value="0">
            </div>
        @endif
    @endauth

    {{-- Payment Method Selection --}}
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Select Payment Method</label>
        
        <div class="space-y-3">
            {{-- Stripe --}}
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition payment-method-option">
                <input type="radio" name="payment_method" value="stripe" checked class="w-5 h-5 text-blue-600">
                <span class="ml-3 flex items-center flex-1">
                    <img src="{{ asset('frontend/img/stripe.svg') }}" alt="Stripe" class="h-8 mr-3" onerror="this.src='https://img.icons8.com/color/48/stripe.png'">
                    <span>
                        <span class="font-medium">Credit / Debit Card</span>
                        <span class="block text-sm text-gray-500">Visa, Mastercard, Amex</span>
                    </span>
                </span>
                <i class="fa-solid fa-lock text-green-500"></i>
            </label>

            {{-- Razorpay --}}
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition payment-method-option">
                <input type="radio" name="payment_method" value="razorpay" class="w-5 h-5 text-blue-600">
                <span class="ml-3 flex items-center flex-1">
                    <img src="{{ asset('frontend/img/razorpay.svg') }}" alt="Razorpay" class="h-8 mr-3" onerror="this.src='https://img.icons8.com/color/48/razorpay.png'">
                    <span>
                        <span class="font-medium">Razorpay</span>
                        <span class="block text-sm text-gray-500">UPI, NetBanking, Cards, Wallets</span>
                    </span>
                </span>
                <i class="fa-solid fa-lock text-green-500"></i>
            </label>

            {{-- Wallet Only (if balance covers full amount) --}}
            @if(isset($walletBalance) && $walletBalance >= ($booking->total_amount ?? 0))
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition payment-method-option">
                    <input type="radio" name="payment_method" value="wallet_only" class="w-5 h-5 text-blue-600">
                    <span class="ml-3 flex items-center flex-1">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fa-solid fa-wallet text-green-600"></i>
                        </div>
                        <span>
                            <span class="font-medium">Wallet Only</span>
                            <span class="block text-sm text-gray-500">Pay entirely from wallet</span>
                        </span>
                    </span>
                </label>
            @endif
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="font-medium mb-3">Price Summary</h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Room Price ({{ $booking->nights ?? 1 }} nights)</span>
                <span id="room-price">₹{{ number_format($booking->subtotal ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Taxes & Fees</span>
                <span id="taxes">₹{{ number_format($booking->tax_amount ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-green-600 hidden" id="coupon-discount-row">
                <span>Coupon Discount</span>
                <span id="coupon-discount">-₹0</span>
            </div>
            <div class="flex justify-between text-purple-600 hidden" id="loyalty-discount-row">
                <span>Loyalty Points</span>
                <span id="loyalty-discount">-₹0</span>
            </div>
            <div class="flex justify-between text-green-600 hidden" id="wallet-payment-row">
                <span>Wallet Payment</span>
                <span id="wallet-payment">-₹0</span>
            </div>
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between font-bold text-lg">
                    <span>Total to Pay</span>
                    <span id="final-total">₹{{ number_format($booking->total_amount ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Loyalty Points Earn Info --}}
    @auth
        @if(Auth::user()->loyalty)
            <div class="mt-4 bg-purple-50 rounded-lg p-3 text-sm">
                <i class="fa-solid fa-award text-purple-600 mr-2"></i>
                You'll earn <strong>{{ round(($booking->total_amount ?? 0) * 0.1) }} points</strong> from this booking!
            </div>
        @endif
    @endauth
</div>

@push('scripts')
<script>
let originalTotal = {{ $booking->total_amount ?? 0 }};
let couponDiscount = 0;
let loyaltyDiscount = 0;
let walletAmount = 0;

function applyCoupon() {
    const code = document.getElementById('coupon_code').value;
    if (!code) return;

    fetch('{{ route("coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            code: code, 
            amount: originalTotal,
            room_id: {{ $booking->room_id ?? 'null' }}
        })
    })
    .then(response => response.json())
    .then(data => {
        const statusEl = document.getElementById('coupon-status');
        statusEl.classList.remove('hidden');
        
        if (data.success) {
            couponDiscount = data.discount;
            document.getElementById('applied_coupon_id').value = data.coupon_id;
            document.getElementById('coupon-discount-row').classList.remove('hidden');
            document.getElementById('coupon-discount').textContent = `-₹${data.discount.toFixed(2)}`;
            statusEl.innerHTML = `<span class="text-green-600"><i class="fa-solid fa-check-circle mr-1"></i> ${data.message}</span>`;
            updateTotal();
        } else {
            statusEl.innerHTML = `<span class="text-red-600"><i class="fa-solid fa-times-circle mr-1"></i> ${data.message}</span>`;
        }
    });
}

function toggleLoyaltyPoints() {
    const slider = document.getElementById('loyalty-points-slider');
    const checkbox = document.getElementById('use_loyalty_points');
    
    if (checkbox.checked) {
        slider.classList.remove('hidden');
        document.getElementById('loyalty_points_range').value = {{ $maxPointsToUse ?? 0 }};
        updateLoyaltyPointsValue();
    } else {
        slider.classList.add('hidden');
        document.getElementById('loyalty_points_range').value = 0;
        loyaltyDiscount = 0;
        document.getElementById('loyalty-discount-row').classList.add('hidden');
        updateTotal();
    }
}

function updateLoyaltyPointsValue() {
    const points = parseInt(document.getElementById('loyalty_points_range').value);
    const saving = points * 0.25;
    
    document.getElementById('points-using').textContent = points.toLocaleString();
    document.getElementById('points-saving').textContent = saving.toFixed(2);
    
    loyaltyDiscount = saving;
    document.getElementById('loyalty-discount-row').classList.remove('hidden');
    document.getElementById('loyalty-discount').textContent = `-₹${saving.toFixed(2)}`;
    updateTotal();
}

function toggleWalletPayment() {
    const checkbox = document.getElementById('use_wallet');
    const balance = parseFloat(checkbox.dataset.balance);
    const remaining = originalTotal - couponDiscount - loyaltyDiscount;
    
    if (checkbox.checked) {
        walletAmount = Math.min(balance, remaining);
        document.getElementById('wallet_amount').value = walletAmount;
        document.getElementById('wallet-payment-row').classList.remove('hidden');
        document.getElementById('wallet-payment').textContent = `-₹${walletAmount.toFixed(2)}`;
    } else {
        walletAmount = 0;
        document.getElementById('wallet_amount').value = 0;
        document.getElementById('wallet-payment-row').classList.add('hidden');
    }
    updateTotal();
}

function updateTotal() {
    const total = Math.max(0, originalTotal - couponDiscount - loyaltyDiscount - walletAmount);
    document.getElementById('final-total').textContent = `₹${total.toFixed(2)}`;
}

// Highlight selected payment method
document.querySelectorAll('.payment-method-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.payment-method-option').forEach(o => o.classList.remove('border-blue-500', 'bg-blue-50'));
        this.classList.add('border-blue-500', 'bg-blue-50');
    });
});
</script>
@endpush
