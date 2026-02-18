{{-- Reviews Section - Include in room detail page --}}
<section id="reviews" class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold">Guest Reviews</h2>
                <div class="flex items-center mt-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= round($room->average_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="ml-2 text-lg font-semibold">{{ number_format($room->average_rating ?? 0, 1) }}</span>
                    <span class="text-gray-500 ml-2">({{ $room->reviews_count ?? 0 }} reviews)</span>
                </div>
            </div>
            @auth
                @if($canReview ?? false)
                    <a href="{{ route('reviews.create', ['room_id' => $room->id]) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fa-solid fa-pen mr-2"></i> Write a Review
                    </a>
                @endif
            @endauth
        </div>

        <!-- Rating Breakdown -->
        @if(($room->reviews_count ?? 0) > 0)
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Overall Score -->
                    <div class="text-center md:text-left">
                        <div class="text-5xl font-bold text-blue-600">{{ number_format($room->average_rating ?? 0, 1) }}</div>
                        <div class="flex items-center justify-center md:justify-start mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-lg {{ $i <= round($room->average_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-gray-500 mt-1">Based on {{ $room->reviews_count ?? 0 }} reviews</p>
                    </div>

                    <!-- Category Ratings -->
                    <div class="space-y-3">
                        @php
                            $categories = [
                                'cleanliness' => ['label' => 'Cleanliness', 'icon' => 'fa-broom'],
                                'comfort' => ['label' => 'Comfort', 'icon' => 'fa-couch'],
                                'location' => ['label' => 'Location', 'icon' => 'fa-location-dot'],
                                'service' => ['label' => 'Service', 'icon' => 'fa-concierge-bell'],
                                'value' => ['label' => 'Value', 'icon' => 'fa-tag'],
                                'amenities' => ['label' => 'Amenities', 'icon' => 'fa-wifi'],
                            ];
                        @endphp
                        @foreach($categories as $key => $category)
                            @php $rating = $categoryRatings[$key] ?? 0; @endphp
                            <div class="flex items-center">
                                <div class="w-24 text-sm text-gray-600">
                                    <i class="fa-solid {{ $category['icon'] }} mr-1 w-4"></i> {{ $category['label'] }}
                                </div>
                                <div class="flex-1 mx-3">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($rating / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="w-8 text-sm font-medium text-right">{{ number_format($rating, 1) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex flex-wrap gap-2">
                        @for($stars = 5; $stars >= 1; $stars--)
                            @php $count = $ratingDistribution[$stars] ?? 0; @endphp
                            <button onclick="filterReviews({{ $stars }})" 
                                    class="px-3 py-1 border rounded-full text-sm hover:bg-gray-50 review-filter">
                                {{ $stars }} <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                                <span class="text-gray-500">({{ $count }})</span>
                            </button>
                        @endfor
                        <button onclick="filterReviews('all')" 
                                class="px-3 py-1 border rounded-full text-sm hover:bg-gray-50 review-filter active">
                            All
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reviews List -->
        <div id="reviews-container">
            @forelse($reviews ?? [] as $review)
                <div class="bg-white rounded-xl shadow-md p-6 mb-4" data-rating="{{ $review->overall_rating }}">
                    <div class="flex items-start">
                        <!-- Avatar -->
                        <img src="{{ $review->user->profile_photo_url ?? asset('frontend/img/default-avatar.png') }}" 
                             alt="{{ $review->user->name }}"
                             class="w-12 h-12 rounded-full object-cover">
                        
                        <div class="ml-4 flex-1">
                            <!-- Header -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold">{{ $review->user->name }}</h4>
                                    <p class="text-gray-500 text-sm">{{ $review->created_at->format('F Y') }}</p>
                                </div>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            <!-- Stay Info -->
                            @if($review->booking)
                                <p class="text-gray-500 text-sm mt-1">
                                    <i class="fa-solid fa-calendar mr-1"></i> 
                                    Stayed {{ $review->booking->nights }} night(s) in {{ $review->booking->check_in->format('F Y') }}
                                </p>
                            @endif

                            <!-- Review Content -->
                            <div class="mt-3">
                                @if($review->title)
                                    <h5 class="font-medium mb-1">{{ $review->title }}</h5>
                                @endif
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            </div>

                            <!-- Pros & Cons -->
                            @if($review->pros || $review->cons)
                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    @if($review->pros)
                                        <div>
                                            <span class="text-green-600 text-sm font-medium">
                                                <i class="fa-solid fa-thumbs-up mr-1"></i> Liked
                                            </span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $review->pros }}</p>
                                        </div>
                                    @endif
                                    @if($review->cons)
                                        <div>
                                            <span class="text-red-600 text-sm font-medium">
                                                <i class="fa-solid fa-thumbs-down mr-1"></i> Could Improve
                                            </span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $review->cons }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Review Photos -->
                            @if($review->photos->count() > 0)
                                <div class="mt-4 flex gap-2 overflow-x-auto">
                                    @foreach($review->photos as $photo)
                                        <img src="{{ asset($photo->photo_path) }}" 
                                             alt="Review photo"
                                             class="w-20 h-20 rounded-lg object-cover cursor-pointer hover:opacity-80"
                                             onclick="openPhotoModal('{{ asset($photo->photo_path) }}')">
                                    @endforeach
                                </div>
                            @endif

                            <!-- Manager Response -->
                            @if($review->manager_response)
                                <div class="mt-4 bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                                    <div class="flex items-center mb-2">
                                        <i class="fa-solid fa-hotel text-blue-600 mr-2"></i>
                                        <span class="font-medium text-sm">Response from Hotel</span>
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $review->manager_response }}</p>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="mt-4 flex items-center gap-4 text-sm">
                                <button onclick="markHelpful({{ $review->id }})" 
                                        class="text-gray-500 hover:text-blue-600 helpful-btn-{{ $review->id }}">
                                    <i class="fa-regular fa-thumbs-up mr-1"></i>
                                    Helpful (<span class="helpful-count">{{ $review->helpful_count ?? 0 }}</span>)
                                </button>
                                <button onclick="reportReview({{ $review->id }})" class="text-gray-500 hover:text-red-600">
                                    <i class="fa-regular fa-flag mr-1"></i> Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white rounded-xl shadow-md">
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-comment text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">No reviews yet</h3>
                    <p class="text-gray-500 mb-4">Be the first to share your experience!</p>
                    @auth
                        @if($canReview ?? false)
                            <a href="{{ route('reviews.create', ['room_id' => $room->id]) }}" 
                               class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Write the First Review
                            </a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>

        <!-- Load More -->
        @if(($reviews ?? collect())->hasMorePages())
            <div class="text-center mt-6">
                <button onclick="loadMoreReviews()" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                    Load More Reviews
                </button>
            </div>
        @endif
    </div>
</section>

<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center">
    <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white text-2xl">
        <i class="fa-solid fa-times"></i>
    </button>
    <img id="modalPhoto" src="" alt="Review photo" class="max-w-full max-h-[90vh] object-contain">
</div>

@push('scripts')
<script>
let currentPage = 1;
let currentFilter = 'all';

function filterReviews(rating) {
    currentFilter = rating;
    document.querySelectorAll('.review-filter').forEach(btn => btn.classList.remove('active', 'bg-blue-100'));
    event.target.classList.add('active', 'bg-blue-100');
    
    document.querySelectorAll('[data-rating]').forEach(review => {
        if (rating === 'all' || parseInt(review.dataset.rating) === rating) {
            review.style.display = 'block';
        } else {
            review.style.display = 'none';
        }
    });
}

function markHelpful(reviewId) {
    fetch(`/reviews/${reviewId}/helpful`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`.helpful-btn-${reviewId}`);
            btn.querySelector('.helpful-count').textContent = data.count;
            btn.classList.add('text-blue-600');
        }
    });
}

function reportReview(reviewId) {
    if (confirm('Are you sure you want to report this review?')) {
        fetch(`/reviews/${reviewId}/report`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert('Review reported. Our team will review it shortly.');
        });
    }
}

function loadMoreReviews() {
    currentPage++;
    fetch(`{{ route('reviews.load-more', $room->id) }}?page=${currentPage}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviews-container').insertAdjacentHTML('beforeend', html);
        });
}

function openPhotoModal(src) {
    document.getElementById('modalPhoto').src = src;
    document.getElementById('photoModal').classList.remove('hidden');
    document.getElementById('photoModal').classList.add('flex');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    document.getElementById('photoModal').classList.remove('flex');
}
</script>
@endpush
