@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Notifications
            </a>
            <h1 class="text-3xl font-bold">Notification Settings</h1>
            <p class="text-gray-600 mt-2">Choose what notifications you want to receive</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6">
                <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('notifications.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Notification Types -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Notification Types</h2>
                <p class="text-gray-600 text-sm mb-6">Select which types of notifications you want to receive</p>

                <div class="space-y-4">
                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Booking Confirmations</div>
                            <p class="text-sm text-gray-500">Receive confirmation when your booking is confirmed</p>
                        </div>
                        <input type="checkbox" name="booking_confirmations" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['booking_confirmations'] ?? true ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Booking Reminders</div>
                            <p class="text-sm text-gray-500">Get reminders before your check-in date</p>
                        </div>
                        <input type="checkbox" name="booking_reminders" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['booking_reminders'] ?? true ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Payment Notifications</div>
                            <p class="text-sm text-gray-500">Notifications about payments, refunds, and wallet</p>
                        </div>
                        <input type="checkbox" name="payment_notifications" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['payment_notifications'] ?? true ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Promotional Offers</div>
                            <p class="text-sm text-gray-500">Special deals, discounts, and seasonal offers</p>
                        </div>
                        <input type="checkbox" name="promotional_offers" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['promotional_offers'] ?? false ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Loyalty Updates</div>
                            <p class="text-sm text-gray-500">Points earned, tier upgrades, and rewards</p>
                        </div>
                        <input type="checkbox" name="loyalty_updates" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['loyalty_updates'] ?? true ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div>
                            <div class="font-medium">Review Reminders</div>
                            <p class="text-sm text-gray-500">Reminder to leave a review after your stay</p>
                        </div>
                        <input type="checkbox" name="review_reminders" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['review_reminders'] ?? true ? 'checked' : '' }}>
                    </label>
                </div>
            </div>

            <!-- Notification Channels -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Notification Channels</h2>
                <p class="text-gray-600 text-sm mb-6">Choose how you want to receive notifications</p>

                <div class="space-y-4">
                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Email Notifications</div>
                                <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <input type="checkbox" name="email_notifications" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['email_notifications'] ?? true ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-mobile text-green-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">SMS Notifications</div>
                                <p class="text-sm text-gray-500">{{ Auth::user()->phone ?? 'Add phone number' }}</p>
                            </div>
                        </div>
                        <input type="checkbox" name="sms_notifications" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['sms_notifications'] ?? false ? 'checked' : '' }}>
                    </label>

                    <label class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fa-solid fa-bell text-purple-600"></i>
                            </div>
                            <div>
                                <div class="font-medium">Push Notifications</div>
                                <p class="text-sm text-gray-500">In-app and browser notifications</p>
                            </div>
                        </div>
                        <input type="checkbox" name="push_notifications" value="1"
                               class="w-5 h-5 text-blue-600 rounded"
                               {{ $preferences['push_notifications'] ?? true ? 'checked' : '' }}>
                    </label>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex gap-4">
                <a href="{{ route('notifications.index') }}" class="flex-1 px-6 py-3 border rounded-lg text-center hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    <i class="fa-solid fa-check mr-2"></i> Save Preferences
                </button>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="mt-8 p-6 border border-red-200 rounded-xl">
            <h3 class="font-semibold text-red-600 mb-2">Unsubscribe from All</h3>
            <p class="text-sm text-gray-600 mb-4">Stop receiving all marketing and promotional notifications. You will still receive essential booking-related notifications.</p>
            <button type="button" onclick="unsubscribeAll()" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                Unsubscribe from All Marketing
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function unsubscribeAll() {
    if (confirm('Are you sure you want to unsubscribe from all marketing notifications?')) {
        document.querySelector('[name="promotional_offers"]').checked = false;
        document.querySelector('form').submit();
    }
}
</script>
@endpush
@endsection
