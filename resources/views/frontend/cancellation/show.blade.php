@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('user.booking') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Bookings
            </a>
            <h1 class="text-3xl font-bold">Cancel Booking</h1>
            <p class="text-gray-600 mt-2">Booking #{{ $booking->code }}</p>
        </div>

        <!-- Warning Banner -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-xl mt-0.5 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-yellow-800">Are you sure you want to cancel?</h3>
                    <p class="text-yellow-700 text-sm mt-1">This action cannot be undone. Please review the refund details below.</p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Booking Details</h3>
            <div class="flex items-start">
                <img src="{{ asset($booking->room->image ?? 'frontend/img/placeholder.jpg') }}" 
                     alt="{{ $booking->room->room_name }}" 
                     class="w-32 h-24 rounded-lg object-cover">
                <div class="ml-4 flex-1">
                    <h4 class="font-semibold">{{ $booking->room->room_name }}</h4>
                    <div class="text-sm text-gray-600 mt-2 space-y-1">
                        <p><i class="fa-solid fa-calendar mr-2"></i> {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                        <p><i class="fa-solid fa-moon mr-2"></i> {{ $booking->nights }} night(s)</p>
                        <p><i class="fa-solid fa-user mr-2"></i> {{ $booking->number_of_guest ?? 1 }} guest(s)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancellation Policy -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Cancellation Policy</h3>
            <div class="flex items-center mb-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $policy->is_free_cancellation ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ $policy->name }}
                </span>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex items-start">
                    <i class="fa-solid fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                    <span>Cancel more than {{ $policy->days_before_partial_refund }} days before check-in: <strong>{{ $policy->full_refund_percentage }}% refund</strong></span>
                </div>
                <div class="flex items-start">
                    <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                    <span>Cancel {{ $policy->days_before_partial_refund }} days or less before check-in: <strong>{{ $policy->partial_refund_percentage }}% refund</strong></span>
                </div>
                <div class="flex items-start">
                    <i class="fa-solid fa-times-circle text-red-500 mt-0.5 mr-3"></i>
                    <span>Cancel within {{ $policy->hours_before_full_charge }} hours of check-in: <strong>No refund</strong></span>
                </div>
            </div>
        </div>

        <!-- Refund Breakdown -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Refund Calculation</h3>
            
            <div class="mb-4 p-4 {{ $refundBreakdown['refund_percentage'] > 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                <div class="flex items-center">
                    <i class="fa-solid {{ $refundBreakdown['refund_percentage'] > 0 ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }} text-xl mr-3"></i>
                    <div>
                        <p class="font-medium">{{ $refundBreakdown['applicable_rule'] }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $refundBreakdown['days_until_checkin'] }} days until check-in
                            ({{ $refundBreakdown['hours_until_checkin'] }} hours)
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3 border-t pt-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Original Booking Amount</span>
                    <span class="font-medium">₹{{ number_format($refundBreakdown['original_amount'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Refund Percentage</span>
                    <span class="font-medium">{{ $refundBreakdown['refund_percentage'] }}%</span>
                </div>
                @if($refundBreakdown['penalty_amount'] > 0)
                <div class="flex justify-between text-red-600">
                    <span>Cancellation Penalty</span>
                    <span class="font-medium">- ₹{{ number_format($refundBreakdown['penalty_amount'], 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-3">
                    <span>Refund Amount</span>
                    <span class="text-green-600">₹{{ number_format($refundBreakdown['refund_amount'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Cancellation Form -->
        <form action="{{ route('cancellation.process', $booking->id) }}" method="POST" class="bg-white rounded-xl shadow-md p-6">
            @csrf
            
            <h3 class="font-semibold text-lg mb-4">Cancellation Reason</h3>
            
            <div class="space-y-3 mb-6">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="reason_type" value="change_of_plans" class="text-blue-600">
                    <span class="ml-3">Change of plans</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="reason_type" value="found_better_option" class="text-blue-600">
                    <span class="ml-3">Found a better option</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="reason_type" value="emergency" class="text-blue-600">
                    <span class="ml-3">Personal emergency</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="reason_type" value="other" class="text-blue-600">
                    <span class="ml-3">Other reason</span>
                </label>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details (Optional)</label>
                <textarea name="reason" rows="3" placeholder="Please tell us more about why you're cancelling..."
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Refund Method -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Refund Method</label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="refund_method" value="wallet" checked class="text-blue-600">
                        <span class="ml-3">
                            <strong>Wallet</strong> - Instant credit (Recommended)
                            <p class="text-sm text-gray-500">Use for future bookings</p>
                        </span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="refund_method" value="original" class="text-blue-600">
                        <span class="ml-3">
                            <strong>Original Payment Method</strong> - 5-7 business days
                            <p class="text-sm text-gray-500">Refund to your card/bank account</p>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('user.booking') }}" class="flex-1 px-6 py-3 border rounded-lg text-center hover:bg-gray-50">
                    Keep Booking
                </a>
                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')" 
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                    <i class="fa-solid fa-times mr-2"></i> Confirm Cancellation
                </button>
            </div>
        </form>

        <!-- Help -->
        <div class="mt-6 text-center text-sm text-gray-500">
            Need help? <a href="{{ route('contact.us') }}" class="text-blue-600 hover:underline">Contact our support team</a>
        </div>
    </div>
</div>
@endsection
