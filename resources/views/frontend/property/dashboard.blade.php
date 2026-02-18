@extends('frontend.main_master')
@section('main')
<div class="container py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-8 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">My Properties Dashboard</h2>
                <a href="{{ route('property.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add New Property</a>
            </div>

            @if(isset($stats))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Total Properties</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_properties'] }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Active Properties</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active_properties'] }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Pending Verification</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_verification'] }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Total Bookings</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
            @endif

            @if($hostProfile)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Host Profile</h3>
                <p><strong>Type:</strong> {{ ucfirst($hostProfile->type) }}</p>
                <p><strong>Verification Status:</strong> 
                    <span class="px-2 py-1 rounded text-sm 
                        {{ $hostProfile->verification_status === 'verified' ? 'bg-green-100 text-green-800' : 
                           ($hostProfile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($hostProfile->verification_status) }}
                    </span>
                </p>
                @if($hostProfile->is_superhost)
                <p class="mt-2"><span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm font-semibold">⭐ Superhost</span></p>
                @endif
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-md p-8">
            <h3 class="text-xl font-bold mb-4">My Properties</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left border">Name</th>
                            <th class="p-3 text-left border">Type</th>
                            <th class="p-3 text-left border">Listing Type</th>
                            <th class="p-3 text-left border">City</th>
                            <th class="p-3 text-left border">Status</th>
                            <th class="p-3 text-left border">Verification</th>
                            <th class="p-3 text-left border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($properties as $property)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">{{ $property->name }}</td>
                            <td class="p-3 border">{{ $property->type->name ?? '-' }}</td>
                            <td class="p-3 border">
                                <span class="px-2 py-1 rounded text-xs 
                                    {{ $property->listing_type === 'hotel' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                </span>
                            </td>
                            <td class="p-3 border">{{ $property->city }}</td>
                            <td class="p-3 border">
                                <span class="px-2 py-1 rounded text-xs 
                                    {{ $property->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </td>
                            <td class="p-3 border">
                                <span class="px-2 py-1 rounded text-xs 
                                    {{ $property->verification_status === 'verified' ? 'bg-green-100 text-green-800' : 
                                       ($property->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($property->verification_status) }}
                                </span>
                            </td>
                            <td class="p-3 border">
                                <a href="{{ route('property.show', $property->id) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                <a href="{{ route('property.edit', $property->id) }}" class="text-green-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">No properties found. <a href="{{ route('property.create') }}" class="text-blue-600 hover:underline">Create your first property</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
