@extends('frontend.main_master')
@section('main')
<div class="container py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit Property</h2>
            <a href="{{ route('property.dashboard') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('property.update', $property->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block font-medium mb-1">Property Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $property->name) }}" 
                           class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Property Type <span class="text-red-500">*</span></label>
                    <select name="property_type_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ $property->property_type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium mb-1">Listing Type</label>
                    <input type="text" value="{{ ucfirst(str_replace('_', ' ', $property->listing_type)) }}" 
                           class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                    <p class="text-sm text-gray-500 mt-1">Listing type cannot be changed after creation.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-medium mb-1">Address <span class="text-red-500">*</span></label>
                    <input type="text" name="address" value="{{ old('address', $property->address) }}" 
                           class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $property->city) }}" 
                           class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">State</label>
                    <input type="text" name="state" value="{{ old('state', $property->state) }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium mb-1">Country <span class="text-red-500">*</span></label>
                    <input type="text" name="country" value="{{ old('country', $property->country) }}" 
                           class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-medium mb-1">Zipcode</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode', $property->zipcode) }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium mb-1">Latitude (for map)</label>
                    <input type="number" step="any" name="latitude" 
                           value="{{ old('latitude', $property->latitude) }}" 
                           class="w-full border rounded px-3 py-2" placeholder="e.g., 19.0760">
                </div>

                <div>
                    <label class="block font-medium mb-1">Longitude (for map)</label>
                    <input type="number" step="any" name="longitude" 
                           value="{{ old('longitude', $property->longitude) }}" 
                           class="w-full border rounded px-3 py-2" placeholder="e.g., 72.8777">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-medium mb-1">Description</label>
                    <textarea name="description" rows="5" 
                              class="w-full border rounded px-3 py-2">{{ old('description', $property->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-medium mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $property->phone) }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $property->email) }}" 
                           class="w-full border rounded px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="instant_book_enabled" value="1" 
                               {{ old('instant_book_enabled', $property->instant_book_enabled) ? 'checked' : '' }} 
                               class="mr-2">
                        <span>Enable Instant Booking</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">If unchecked, guests must request to book and wait for approval.</p>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold mb-2">Verification Status</h3>
                        <p class="text-sm">
                            <span class="px-2 py-1 rounded text-xs 
                                {{ $property->verification_status === 'verified' ? 'bg-green-100 text-green-800' : 
                                   ($property->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($property->verification_status) }}
                            </span>
                        </p>
                        @if($property->verification_notes)
                        <p class="text-sm text-gray-600 mt-2">{{ $property->verification_notes }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update Property
                </button>
                <a href="{{ route('property.dashboard') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


