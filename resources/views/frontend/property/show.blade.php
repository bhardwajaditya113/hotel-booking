@extends('frontend.main_master')
@section('main')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Inner Banner -->
<div class="inner-banner inner-bg10">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Property Details</li>
            </ul>
            <h3>{{ $property->name }}</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Property Details Area -->
<div class="room-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="room-details-side">
                    <!-- Property Info Card -->
                    <div class="side-bar-form mb-4">
                        <h3>Property Information</h3>
                        <div class="property-info">
                            <p><strong>Type:</strong> {{ $property->type->name ?? 'N/A' }}</p>
                            <p><strong>Listing Type:</strong> 
                                <span class="badge {{ $property->listing_type === 'hotel' ? 'bg-blue' : 'bg-green' }}">
                                    {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                </span>
                            </p>
                            <p><strong>Location:</strong> {{ $property->city }}, {{ $property->state ?? $property->country }}</p>
                            @if($property->average_rating)
                            <p><strong>Rating:</strong> 
                                @for($i = 1; $i <= 5; $i++)
                                    <i class='bx {{ $i <= round($property->average_rating) ? 'bxs-star' : 'bx-star' }} text-warning'></i>
                                @endfor
                                ({{ number_format($property->average_rating, 1) }}) - {{ $property->total_reviews }} reviews
                            </p>
                            @endif
                            @if($property->isVerified())
                            <p class="text-success"><i class='bx bx-check-circle'></i> Verified Property</p>
                            @endif
                        </div>
                    </div>

                    <!-- Host Information -->
                    @if($property->host)
                    <div class="side-bar-form mb-4">
                        <h3>Host Information</h3>
                        <div class="host-info">
                            <p><strong>Host:</strong> {{ $property->host->name }}</p>
                            @if($property->host->hostProfile)
                                @if($property->host->hostProfile->is_superhost)
                                <p class="text-purple"><i class='bx bx-star'></i> Superhost</p>
                                @endif
                                @if($property->host->hostProfile->average_rating)
                                <p><strong>Host Rating:</strong> {{ number_format($property->host->hostProfile->average_rating, 1) }}/5</p>
                                @endif
                            @endif
                            @auth
                            <a href="{{ route('messages.start') }}?receiver_id={{ $property->host->id }}&property_id={{ $property->id }}" 
                               class="btn btn-primary mt-2">
                                <i class='bx bx-message'></i> Message Host
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary mt-2">Login to Message Host</a>
                            @endauth
                        </div>
                    </div>
                    @endif

                    <!-- Booking Form -->
                    @if($property->activeRooms->count() > 0)
                    <div class="side-bar-form">
                        <h3>Book This Property</h3>
                        <form action="{{ route('property.booking.store') }}" method="POST" id="property-booking-form">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Select Room</label>
                                <select name="room_id" class="form-control" required id="room-select">
                                    <option value="">Choose a room</option>
                                    @foreach($property->activeRooms as $room)
                                    <option value="{{ $room->id }}" 
                                            data-price="{{ $room->price }}"
                                            data-capacity="{{ $room->total_adult ?? 1 }}">
                                        {{ $room->type->name ?? 'Room' }} - ₹{{ number_format($room->price) }}/night
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Check In</label>
                                <input type="date" name="check_in" class="form-control" 
                                       min="{{ date('Y-m-d') }}" required id="check_in">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Check Out</label>
                                <input type="date" name="check_out" class="form-control" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required id="check_out">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="persion" class="form-control" 
                                       min="1" value="1" required id="guests">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Number of Rooms</label>
                                <input type="number" name="number_of_rooms" class="form-control" 
                                       min="1" value="1" required id="number_of_rooms">
                            </div>

                            <div class="mb-3">
                                <div class="alert alert-info" id="price-preview" style="display: none;">
                                    <strong>Estimated Total: <span id="total-price">₹0</span></strong>
                                </div>
                            </div>

                            @auth
                            <button type="submit" class="btn btn-primary w-100">Book Now</button>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Book</a>
                            @endauth
                        </form>
                    </div>
                    @endif

                    <!-- Available Rooms -->
                    @if($property->activeRooms->count() > 0)
                    <div class="side-bar-form mt-4">
                        <h3>Available Rooms</h3>
                        <div class="rooms-list">
                            @foreach($property->activeRooms->take(5) as $room)
                            <div class="room-item mb-3 p-3 border rounded">
                                <h5>{{ $room->type->name ?? 'Room' }}</h5>
                                <p class="mb-1"><strong>Price:</strong> ₹{{ number_format($room->price) }}/night</p>
                                <p class="mb-1"><strong>Capacity:</strong> {{ $room->total_adult ?? 'N/A' }} guests</p>
                                <a href="{{ url('room/details/'.$room->id) }}" class="btn btn-sm btn-outline-primary">View Room</a>
                            </div>
                            @endforeach
                            @if($property->activeRooms->count() > 5)
                            <p class="text-center"><a href="#rooms-section">View All {{ $property->activeRooms->count() }} Rooms</a></p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-8">
                <div class="room-details-article">
                    <!-- Property Images -->
                    @if($property->images && count($property->images) > 0)
                    <div class="room-details-slider owl-carousel owl-theme">
                        @foreach($property->images as $image)
                        <div class="room-details-item">
                            <img src="{{ asset('upload/properties/'.$image) }}" alt="{{ $property->name }}">
                        </div>
                        @endforeach
                    </div>
                    @elseif($property->cover_image)
                    <div class="room-details-item">
                        <img src="{{ asset('upload/properties/'.$property->cover_image) }}" alt="{{ $property->name }}">
                    </div>
                    @else
                    <div class="room-details-item">
                        <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ $property->name }}">
                    </div>
                    @endif

                    <!-- Property Title -->
                    <div class="room-details-title">
                        <h2>{{ $property->name }}</h2>
                        <ul>
                            <li><b>{{ $property->formatted_address }}</b></li>
                            @if($property->activeRooms->count() > 0)
                            <li><b>From ₹{{ number_format($property->activeRooms->min('price')) }}/night</b></li>
                            @endif
                        </ul>
                    </div>

                    <!-- Property Description -->
                    <div class="room-details-content">
                        <p>{!! $property->description ?? 'No description available.' !!}</p>
                    </div>

                    <!-- Property Amenities -->
                    @if($property->amenities && count($property->amenities) > 0)
                    <div class="side-bar-plan mt-4">
                        <h3>Property Amenities</h3>
                        <ul>
                            @foreach($property->amenities as $amenityId)
                                @php
                                    $amenity = \App\Models\Amenity::find($amenityId);
                                @endphp
                                @if($amenity)
                                <li><i class='bx bx-check'></i> {{ $amenity->name }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- House Rules -->
                    @if($property->house_rules)
                    <div class="side-bar-plan mt-4">
                        <h3>House Rules</h3>
                        <p>{!! nl2br(e($property->house_rules)) !!}</p>
                    </div>
                    @endif

                    <!-- Cancellation Policy -->
                    @if($property->cancellation_policy_text)
                    <div class="side-bar-plan mt-4">
                        <h3>Cancellation Policy</h3>
                        <p>{!! nl2br(e($property->cancellation_policy_text)) !!}</p>
                    </div>
                    @endif

                    <!-- Location Map -->
                    @if($property->latitude && $property->longitude)
                    <div class="mt-4">
                        <h3>Location</h3>
                        <div id="property-map" style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden;">
                            <!-- Google Maps will be embedded here -->
                            <iframe 
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                style="border:0" 
                                src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_key', '') }}&q={{ $property->latitude }},{{ $property->longitude }}" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                    @endif

                    <!-- All Rooms Section -->
                    @if($property->activeRooms->count() > 0)
                    <div id="rooms-section" class="mt-5">
                        <h3>All Rooms ({{ $property->activeRooms->count() }})</h3>
                        <div class="row">
                            @foreach($property->activeRooms as $room)
                            <div class="col-lg-6 mb-4">
                                <div class="room-card-two">
                                    <div class="row align-items-center">
                                        <div class="col-lg-5 col-md-4 p-0">
                                            <div class="room-card-img">
                                                <a href="{{ url('room/details/'.$room->id) }}">
                                                    <img src="{{ asset('upload/roomimg/'.$room->image) }}" alt="{{ $room->type->name ?? 'Room' }}">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-8 p-0">
                                            <div class="room-card-content">
                                                <h3>
                                                    <a href="{{ url('room/details/'.$room->id) }}">
                                                        {{ $room->type->name ?? 'Room' }}
                                                    </a>
                                                </h3>
                                                <span>₹{{ number_format($room->price) }} / Per Night</span>
                                                @if($room->average_rating)
                                                <div class="rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class='bx {{ $i <= round($room->average_rating) ? 'bxs-star' : 'bx-star' }}'></i>
                                                    @endfor
                                                </div>
                                                @endif
                                                <p>{{ $room->short_desc ?? 'No description' }}</p>
                                                <ul>
                                                    <li><i class='bx bx-user'></i> {{ $room->total_adult ?? 'N/A' }} Person</li>
                                                    @if($room->size)
                                                    <li><i class='bx bx-expand'></i> {{ $room->size }}ft²</li>
                                                    @endif
                                                </ul>
                                                <a href="{{ url('room/details/'.$room->id) }}" class="btn btn-primary">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Reviews Section -->
                    @if($property->reviews && $property->reviews->count() > 0)
                    <div class="mt-5">
                        <h3>Reviews ({{ $property->total_reviews }})</h3>
                        @foreach($property->reviews->take(5) as $review)
                        <div class="review-item mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                                <div class="rating">
                                    @php
                                        $rating = $review->rating_overall ?? $review->average_rating ?? 0;
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class='bx {{ $i <= $rating ? 'bxs-star' : 'bx-star' }} text-warning'></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-muted small">{{ $review->created_at->format('M d, Y') }}</p>
                            <p>{{ $review->review_text ?? $review->comment ?? 'No comment' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Property Details Area End -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room-select');
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const guests = document.getElementById('guests');
    const numberOfRooms = document.getElementById('number_of_rooms');
    const pricePreview = document.getElementById('price-preview');
    const totalPrice = document.getElementById('total-price');

    // Only run if booking form elements exist
    if (!roomSelect || !checkIn || !checkOut || !numberOfRooms || !pricePreview || !totalPrice) {
        return; // Exit if elements don't exist
    }

    function calculatePrice() {
        if (!roomSelect.value || !checkIn.value || !checkOut.value) {
            if (pricePreview) pricePreview.style.display = 'none';
            return;
        }

        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        if (!selectedOption) return;
        
        const pricePerNight = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkInDate = new Date(checkIn.value);
        const checkOutDate = new Date(checkOut.value);
        const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
        
        if (nights > 0 && pricePreview && totalPrice) {
            const rooms = parseInt(numberOfRooms.value) || 1;
            const total = pricePerNight * nights * rooms;
            totalPrice.textContent = '₹' + total.toLocaleString('en-IN');
            pricePreview.style.display = 'block';
        } else {
            if (pricePreview) pricePreview.style.display = 'none';
        }
    }

    if (roomSelect) roomSelect.addEventListener('change', calculatePrice);
    if (checkIn) {
        checkIn.addEventListener('change', function() {
            if (checkIn.value && checkOut) {
                const minDate = new Date(checkIn.value);
                minDate.setDate(minDate.getDate() + 1);
                checkOut.min = minDate.toISOString().split('T')[0];
            }
            calculatePrice();
        });
    }
    if (checkOut) checkOut.addEventListener('change', calculatePrice);
    if (numberOfRooms) numberOfRooms.addEventListener('change', calculatePrice);
});
</script>
@endsection

