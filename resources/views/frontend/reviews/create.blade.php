@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('user.booking') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Bookings
            </a>
            <h1 class="text-3xl font-bold">Write a Review</h1>
            <p class="text-gray-600 mt-2">Share your experience at {{ $booking->room->room_name ?? 'our hotel' }}</p>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gray-50 rounded-xl p-6 mb-8">
            <div class="flex items-center">
                <img src="{{ asset($booking->room->image ?? 'frontend/img/placeholder.jpg') }}" 
                     alt="{{ $booking->room->room_name }}" 
                     class="w-24 h-24 rounded-lg object-cover">
                <div class="ml-4">
                    <h3 class="font-semibold text-lg">{{ $booking->room->room_name }}</h3>
                    <p class="text-gray-500">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                    <p class="text-gray-500">Booking #{{ $booking->code }}</p>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <form action="{{ route('review.store', $booking->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md p-6">
            @csrf
            
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-red-600">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Overall Rating -->
            <div class="mb-8">
                <label class="block text-lg font-semibold mb-4">Overall Rating *</label>
                <div class="flex gap-2" id="overallRating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setRating('overall_rating', {{ $i }})" 
                            class="star-btn text-4xl text-gray-300 hover:text-yellow-400 transition" data-rating="{{ $i }}">
                        <i class="fa-solid fa-star"></i>
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="overall_rating" id="overall_rating" value="{{ old('overall_rating') }}" required>
                <p class="text-sm text-gray-500 mt-2" id="overallRatingText">Select your rating</p>
            </div>

            <!-- Detailed Ratings -->
            <div class="mb-8">
                <label class="block text-lg font-semibold mb-4">Rate Different Aspects</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        'cleanliness_rating' => ['label' => 'Cleanliness', 'icon' => 'fa-sparkles'],
                        'location_rating' => ['label' => 'Location', 'icon' => 'fa-location-dot'],
                        'value_rating' => ['label' => 'Value for Money', 'icon' => 'fa-indian-rupee-sign'],
                        'service_rating' => ['label' => 'Service', 'icon' => 'fa-concierge-bell'],
                        'amenities_rating' => ['label' => 'Amenities', 'icon' => 'fa-wifi'],
                    ] as $field => $info)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fa-solid {{ $info['icon'] }} text-gray-400 mr-3"></i>
                            <span>{{ $info['label'] }}</span>
                        </div>
                        <div class="flex gap-1" id="{{ $field }}Stars">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating('{{ $field }}', {{ $i }})" 
                                    class="star-btn-sm text-lg text-gray-300 hover:text-yellow-400 transition" data-rating="{{ $i }}">
                                <i class="fa-solid fa-star"></i>
                            </button>
                            @endfor
                        </div>
                        <input type="hidden" name="{{ $field }}" id="{{ $field }}" value="{{ old($field) }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Trip Type -->
            <div class="mb-6">
                <label class="block font-medium mb-2">Trip Type</label>
                <div class="flex flex-wrap gap-3">
                    @foreach([
                        'business' => 'Business',
                        'leisure' => 'Leisure',
                        'family' => 'Family',
                        'couple' => 'Couple',
                        'solo' => 'Solo',
                    ] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="trip_type" value="{{ $value }}" class="hidden peer"
                               {{ old('trip_type') == $value ? 'checked' : '' }}>
                        <span class="inline-block px-4 py-2 border rounded-full peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:border-blue-300">
                            {{ $label }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Review Title -->
            <div class="mb-6">
                <label class="block font-medium mb-2">Review Title</label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       placeholder="Summarize your stay in a few words"
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Review Text -->
            <div class="mb-6">
                <label class="block font-medium mb-2">Your Review *</label>
                <textarea name="review" rows="6" required minlength="20"
                          placeholder="Tell us about your experience. What did you like or dislike? Would you recommend this room to others?"
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('review') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Minimum 20 characters</p>
            </div>

            <!-- Pros & Cons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-medium mb-2">
                        <i class="fa-solid fa-thumbs-up text-green-500 mr-2"></i> What did you like?
                    </label>
                    <textarea name="pros" rows="3" placeholder="List the positives..."
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('pros') }}</textarea>
                </div>
                <div>
                    <label class="block font-medium mb-2">
                        <i class="fa-solid fa-thumbs-down text-red-500 mr-2"></i> What could be improved?
                    </label>
                    <textarea name="cons" rows="3" placeholder="List areas for improvement..."
                              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('cons') }}</textarea>
                </div>
            </div>

            <!-- Photo Upload -->
            <div class="mb-8">
                <label class="block font-medium mb-2">Add Photos (Optional)</label>
                <p class="text-sm text-gray-500 mb-3">Share photos of your stay (max 5 photos, 5MB each)</p>
                <div class="border-2 border-dashed rounded-lg p-6 text-center" id="dropZone">
                    <input type="file" name="photos[]" multiple accept="image/*" class="hidden" id="photoInput" max="5">
                    <i class="fa-solid fa-cloud-upload text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Drag photos here or <button type="button" onclick="document.getElementById('photoInput').click()" class="text-blue-600 hover:underline">browse</button></p>
                </div>
                <div id="photoPreview" class="flex flex-wrap gap-4 mt-4"></div>
            </div>

            <!-- Terms -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" required class="mt-1 rounded text-blue-600">
                    <span class="ml-2 text-sm text-gray-600">
                        I confirm this review is based on my own experience and is my genuine opinion. 
                        I understand that reviews may be published on our website.
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <a href="{{ route('user.booking') }}" class="px-6 py-3 border rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Submit Review
                </button>
            </div>

            <!-- Bonus Info -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg text-sm">
                <i class="fa-solid fa-gift text-blue-600 mr-2"></i>
                <strong>Earn 50 bonus loyalty points</strong> for leaving a review! Add photos to earn 25 more points.
            </div>
        </form>
    </div>
</div>

<script>
const ratingTexts = {
    1: 'Poor',
    2: 'Fair',
    3: 'Good',
    4: 'Very Good',
    5: 'Excellent'
};

function setRating(field, rating) {
    document.getElementById(field).value = rating;
    
    // Update stars visual
    const containerId = field === 'overall_rating' ? 'overallRating' : field + 'Stars';
    const stars = document.querySelectorAll(`#${containerId} .star-btn, #${containerId} .star-btn-sm`);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
    
    // Update text for overall rating
    if (field === 'overall_rating') {
        document.getElementById('overallRatingText').textContent = ratingTexts[rating];
    }
}

// Photo preview
document.getElementById('photoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).slice(0, 5).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-24 h-24 object-cover rounded-lg">
                <button type="button" onclick="this.parentElement.remove()" 
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// Drag and drop
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-blue-500', 'bg-blue-50');
});
dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
});
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    document.getElementById('photoInput').files = e.dataTransfer.files;
    document.getElementById('photoInput').dispatchEvent(new Event('change'));
});
</script>
@endsection
