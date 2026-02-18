@extends('frontend.main_master')
@section('main')
<div class="container py-8">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6">List a New Property</h2>
        <form action="{{ route('property.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-1">Property Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Listing Type <span class="text-red-500">*</span></label>
                <select name="listing_type" class="w-full border rounded px-3 py-2" required onchange="toggleListingType(this.value)">
                    <option value="">Select listing type</option>
                    <option value="hotel">Hotel (OYO Style)</option>
                    <option value="unique_stay">Unique Stay (Airbnb Style)</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Hotels are for businesses with multiple rooms. Unique stays are for individual hosts listing unique properties.</p>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Property Type <span class="text-red-500">*</span></label>
                <select name="property_type_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select type</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Address</label>
                <input type="text" name="address" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">City</label>
                <input type="text" name="city" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">State</label>
                <input type="text" name="state" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Country</label>
                <input type="text" name="country" class="w-full border rounded px-3 py-2" value="India" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Zipcode</label>
                <input type="text" name="zipcode" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Phone</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Latitude (Optional - for map search)</label>
                <input type="number" step="any" name="latitude" class="w-full border rounded px-3 py-2" placeholder="e.g., 19.0760">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Longitude (Optional - for map search)</label>
                <input type="number" step="any" name="longitude" class="w-full border rounded px-3 py-2" placeholder="e.g., 72.8777">
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="instant_book_enabled" value="1" checked class="mr-2">
                    <span>Enable Instant Booking</span>
                </label>
                <p class="text-sm text-gray-500 mt-1">If unchecked, guests must request to book and wait for approval.</p>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold">Submit Property</button>
        </form>
    </div>
</div>
<script>
function toggleListingType(value) {
    // You can add dynamic form changes here based on listing type
    console.log('Listing type changed to:', value);
}
</script>
@endsection
