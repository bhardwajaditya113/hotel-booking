@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white mb-8">
        <h1 class="text-3xl font-bold mb-2">Find Your Perfect Room</h1>
        <p class="text-blue-100 mb-6">Search from our curated collection of rooms</p>
        
        <!-- Quick Search Form -->
        <form action="{{ route('search.results') }}" method="GET" class="bg-white rounded-xl p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check In</label>
                    <input type="date" name="check_in" value="{{ request('check_in', date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check Out</label>
                    <input type="date" name="check_out" value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
                    <select name="guests" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-800">
                        @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        <i class="fa-solid fa-search mr-2"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Filters</h3>
                    <a href="{{ route('search.index') }}" class="text-sm text-blue-600 hover:underline">Clear All</a>
                </div>

                <form id="filterForm" action="{{ route('search.results') }}" method="GET">
                    <!-- Hidden fields for dates and guests -->
                    <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                    <input type="hidden" name="check_out" value="{{ request('check_out') }}">
                    <input type="hidden" name="guests" value="{{ request('guests') }}">

                    <!-- Property Type (Hotel, Home, Apartment, etc.) -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Property Type</h4>
                        <div class="space-y-2">
                            @foreach(\App\Models\PropertyType::all() as $ptype)
                            <label class="flex items-center">
                                <input type="radio" name="property_type" value="{{ $ptype->id }}" 
                                       {{ request('property_type') == $ptype->id ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm">{{ $ptype->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Property City -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">City</h4>
                        <input type="text" name="property_city" value="{{ request('property_city') }}" placeholder="e.g. Mumbai, Delhi" class="w-full px-3 py-2 border rounded text-sm">
                    </div>

                    <!-- Property Name -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Property Name</h4>
                        <input type="text" name="property_name" value="{{ request('property_name') }}" placeholder="e.g. Taj, Cozy Home" class="w-full px-3 py-2 border rounded text-sm">
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Price Range</h4>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Min" 
                                   value="{{ request('min_price') }}"
                                   class="w-full px-3 py-2 border rounded text-sm">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Max"
                                   value="{{ request('max_price') }}"
                                   class="w-full px-3 py-2 border rounded text-sm">
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button type="button" onclick="setPriceRange(0, 2000)" class="text-xs px-2 py-1 border rounded hover:bg-gray-100">Under ₹2000</button>
                            <button type="button" onclick="setPriceRange(2000, 5000)" class="text-xs px-2 py-1 border rounded hover:bg-gray-100">₹2000-5000</button>
                            <button type="button" onclick="setPriceRange(5000, 10000)" class="text-xs px-2 py-1 border rounded hover:bg-gray-100">₹5000-10000</button>
                        </div>
                    </div>

                    <!-- Room Type -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Room Type</h4>
                        <div class="space-y-2">
                            @foreach($roomTypes as $type)
                            <label class="flex items-center">
                                <input type="radio" name="room_type" value="{{ $type->id }}" 
                                       {{ request('room_type') == $type->id ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm">{{ $type->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Guest Rating</h4>
                        <div class="space-y-2">
                            @foreach([5, 4, 3] as $rating)
                            <label class="flex items-center">
                                <input type="radio" name="min_rating" value="{{ $rating }}"
                                       {{ request('min_rating') == $rating ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm flex items-center">
                                    @for($i = 0; $i < $rating; $i++)
                                    <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                                    @endfor
                                    <span class="ml-1">{{ $rating }}+ stars</span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Popular Amenities</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($amenityCategories->take(2) as $category)
                                @foreach($category->activeAmenities->take(5) as $amenity)
                                <label class="flex items-center">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                           {{ in_array($amenity->id, request('amenities', [])) ? 'checked' : '' }}
                                           class="rounded text-blue-600">
                                    <span class="ml-2 text-sm">
                                        <i class="fa-solid {{ $amenity->icon }} mr-1 text-gray-400"></i>
                                        {{ $amenity->name }}
                                    </span>
                                </label>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Property Type</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags->take(8) as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden peer"
                                       {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}>
                                <span class="inline-block px-3 py-1 border rounded-full text-sm peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:border-blue-300">
                                    {{ $tag->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Special Features -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Special Features</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="instant_book" value="1"
                                       {{ request('instant_book') ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm"><i class="fa-solid fa-bolt text-yellow-500 mr-1"></i> Instant Book</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="free_cancellation" value="1"
                                       {{ request('free_cancellation') ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm"><i class="fa-solid fa-check-circle text-green-500 mr-1"></i> Free Cancellation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="breakfast_included" value="1"
                                       {{ request('breakfast_included') ? 'checked' : '' }}
                                       class="rounded text-blue-600">
                                <span class="ml-2 text-sm"><i class="fa-solid fa-mug-hot text-orange-500 mr-1"></i> Breakfast Included</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <div class="lg:w-3/4">
            <!-- Sort & View Options -->
            <div class="flex flex-wrap justify-between items-center mb-6">
                <p class="text-gray-600">
                    Showing rooms matching your criteria
                </p>
                <div class="flex gap-4 mt-2 sm:mt-0">
                    <select name="sort" onchange="updateSort(this.value)" class="px-4 py-2 border rounded-lg">
                        <option value="recommended" {{ request('sort') == 'recommended' ? 'selected' : '' }}>Recommended</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Guest Rating</option>
                        <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most Reviewed</option>
                    </select>
                    <div class="flex border rounded-lg overflow-hidden">
                        <button onclick="setView('grid')" class="view-btn px-3 py-2 bg-blue-600 text-white" data-view="grid">
                            <i class="fa-solid fa-grip"></i>
                        </button>
                        <button onclick="setView('list')" class="view-btn px-3 py-2 hover:bg-gray-100" data-view="list">
                            <i class="fa-solid fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results will be loaded here -->
            <div id="searchResults" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Placeholder cards -->
                @for($i = 0; $i < 6; $i++)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition animate-pulse">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-4">
                        <div class="h-6 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-2/3 mb-4"></div>
                        <div class="flex justify-between">
                            <div class="h-8 bg-gray-200 rounded w-1/4"></div>
                            <div class="h-8 bg-gray-200 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-8">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function setPriceRange(min, max) {
    document.querySelector('input[name="min_price"]').value = min || '';
    document.querySelector('input[name="max_price"]').value = max || '';
}

function updateSort(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location = url;
}

function setView(view) {
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('hover:bg-gray-100');
    });
    document.querySelector(`[data-view="${view}"]`).classList.add('bg-blue-600', 'text-white');
    document.querySelector(`[data-view="${view}"]`).classList.remove('hover:bg-gray-100');
    
    const results = document.getElementById('searchResults');
    if (view === 'list') {
        results.classList.remove('grid-cols-2');
        results.classList.add('grid-cols-1');
    } else {
        results.classList.add('grid-cols-2');
        results.classList.remove('grid-cols-1');
    }
}

// Auto-submit filters on change
document.querySelectorAll('#filterForm input[type="checkbox"], #filterForm input[type="radio"]').forEach(input => {
    input.addEventListener('change', () => {
        // Optional: Auto-submit on filter change
        // document.getElementById('filterForm').submit();
    });
});

// Date validation
document.querySelector('input[name="check_in"]').addEventListener('change', function() {
    const checkOut = document.querySelector('input[name="check_out"]');
    const minDate = new Date(this.value);
    minDate.setDate(minDate.getDate() + 1);
    checkOut.min = minDate.toISOString().split('T')[0];
    if (new Date(checkOut.value) <= new Date(this.value)) {
        checkOut.value = minDate.toISOString().split('T')[0];
    }
});
</script>
@endsection
