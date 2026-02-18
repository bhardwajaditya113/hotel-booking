@extends('frontend.main_master')
@section('main')

<!-- Page Header -->
<section class="page-header py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white fw-bold mb-2">Search Properties</h1>
                <p class="text-white mb-0" style="opacity: 0.9;">Find your perfect stay</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4 sticky-top" style="top: 20px;">
                <h4 class="fw-bold mb-4">
                    <i class='bx bx-filter-alt me-2 text-primary'></i>Filters
                </h4>
                
                <form method="GET" action="{{ route('search.results') }}" id="filter-form">
                    <input type="hidden" name="search_mode" value="properties">
                    
                    <!-- Listing Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Listing Type</label>
                        <select name="listing_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="hotel" {{ request('listing_type') == 'hotel' ? 'selected' : '' }}>Hotels</option>
                            <option value="unique_stay" {{ request('listing_type') == 'unique_stay' ? 'selected' : '' }}>Unique Stays</option>
                        </select>
                    </div>

                    <!-- Property Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Property Type</label>
                        <select name="property_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach(\App\Models\PropertyType::all() as $type)
                            <option value="{{ $type->id }}" {{ request('property_type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">
                            <i class='bx bx-map me-1'></i>City
                        </label>
                        <input type="text" name="city" value="{{ request('city') }}" 
                               class="form-control" placeholder="Enter city">
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Price Range (per night)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       class="form-control" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       class="form-control" placeholder="Max">
                            </div>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Minimum Rating</label>
                        <select name="min_rating" class="form-select">
                            <option value="">Any Rating</option>
                            <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                            <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                            <option value="5" {{ request('min_rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        </select>
                    </div>

                    <!-- Instant Book -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="instant_book" value="1" 
                                   id="instant_book" {{ request('instant_book') ? 'checked' : '' }}>
                            <label class="form-check-label" for="instant_book">
                                Instant Book Only
                            </label>
                        </div>
                    </div>

                    <!-- Verified Only -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="verified_only" value="1" 
                                   id="verified_only" {{ request('verified_only') ? 'checked' : '' }}>
                            <label class="form-check-label" for="verified_only">
                                Verified Properties Only
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold mb-2">
                        <i class='bx bx-check me-2'></i>Apply Filters
                    </button>
                    <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" 
                       class="btn btn-outline-secondary w-100 rounded-pill">
                        <i class='bx bx-x me-2'></i>Clear Filters
                    </a>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Properties</h2>
                    <p class="text-muted mb-0">
                        <span class="fw-bold text-primary">{{ $properties->total() }}</span> properties found
                    </p>
                </div>
                <div>
                    <select name="sort" class="form-select" onchange="updateSort(this.value)" style="min-width: 200px;">
                        <option value="">Sort By</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most Reviews</option>
                    </select>
                </div>
            </div>

            @if($properties->count() > 0)
            <div class="row g-4">
                @foreach($properties as $property)
                <div class="col-12">
                    <div class="property-card bg-white rounded-4 shadow-sm overflow-hidden h-100 hover-lift">
                        <div class="row g-0">
                            <!-- Property Image -->
                            <div class="col-md-4">
                                <div class="property-image position-relative" style="height: 250px; overflow: hidden;">
                                    <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}" 
                                         class="w-100 h-100" style="object-fit: cover;">
                                    @if($property->isVerified())
                                    <span class="badge bg-success position-absolute top-0 end-0 m-3">
                                        <i class='bx bx-check-circle me-1'></i>Verified
                                    </span>
                                    @endif
                                    @if($property->host && $property->host->hostProfile && $property->host->hostProfile->is_superhost)
                                    <span class="badge position-absolute top-0 start-0 m-3" style="background-color: #6f42c1;">
                                        ⭐ Superhost
                                    </span>
                                    @endif
                                    @if($property->is_featured)
                                    <span class="badge bg-warning position-absolute bottom-0 start-0 m-3">
                                        <i class='bx bx-star me-1'></i>Featured
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Property Info -->
                            <div class="col-md-8">
                                <div class="p-4 h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h3 class="fw-bold mb-2">
                                                <a href="{{ route('property.show', $property->id) }}" class="text-dark text-decoration-none">
                                                    {{ $property->name }}
                                                </a>
                                            </h3>
                                            <p class="text-muted mb-2">
                                                <i class='bx bx-map text-primary me-1'></i> 
                                                {{ $property->city }}{{ $property->state ? ', ' . $property->state : '' }}{{ $property->country ? ', ' . $property->country : '' }}
                                            </p>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                <span class="badge {{ $property->listing_type === 'hotel' ? 'bg-primary' : 'bg-success' }}">
                                                    {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                                </span>
                                                @if($property->type)
                                                <span class="badge bg-secondary">
                                                    {{ $property->type->name }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $avgRating = $property->average_rating ?? ($property->reviews->count() > 0 ? $property->reviews->avg('overall_rating') : 0);
                                                $reviewCount = $property->reviews->count();
                                            @endphp
                                            @if($avgRating > 0)
                                            <div class="d-flex align-items-center justify-content-end gap-1 mb-2">
                                                <i class='bx bxs-star text-warning fs-5'></i>
                                                <span class="fw-bold">{{ number_format($avgRating, 1) }}</span>
                                                <span class="text-muted small">({{ $reviewCount }})</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-muted mb-3 flex-grow-1" style="line-height: 1.6;">
                                        {{ \Illuminate\Support\Str::limit($property->description ?? 'Beautiful property in a great location with modern amenities.', 150) }}
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                        <div>
                                            @php
                                                $minPrice = $property->activeRooms->min('price') ?? 0;
                                                $maxPrice = $property->activeRooms->max('price') ?? 0;
                                            @endphp
                                            @if($minPrice > 0)
                                            <div>
                                                <span class="h4 fw-bold text-primary mb-0">₹{{ number_format($minPrice) }}</span>
                                                <span class="text-muted">/night</span>
                                                @if($maxPrice > $minPrice)
                                                <span class="text-muted small"> - ₹{{ number_format($maxPrice) }}</span>
                                                @endif
                                            </div>
                                            @else
                                            <span class="text-muted">Price on request</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('property.show', $property->id) }}" 
                                           class="btn btn-primary rounded-pill px-4 fw-bold">
                                            View Details <i class='bx bx-arrow-right ms-1'></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $properties->links() }}
            </div>
            @else
            <div class="bg-white rounded-4 shadow-sm p-5 text-center">
                <i class='bx bx-building-house text-muted mb-3' style="font-size: 4rem;"></i>
                <h4 class="fw-bold mb-3">No properties found</h4>
                <p class="text-muted mb-4">Try adjusting your filters or search criteria</p>
                <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" 
                   class="btn btn-primary rounded-pill px-4">
                    <i class='bx bx-refresh me-2'></i>Clear Filters
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.property-card {
    transition: all 0.3s ease;
}

.property-card:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.property-image img {
    transition: transform 0.5s ease;
}

.property-card:hover .property-image img {
    transform: scale(1.05);
}

.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 10;
}
</style>

<script>
function updateSort(value) {
    const form = document.getElementById('filter-form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'sort';
    input.value = value;
    form.appendChild(input);
    form.submit();
}
</script>

@endsection
