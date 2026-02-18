{{-- Review Items Partial - For AJAX Loading --}}
@foreach($reviews as $review)
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
                    Stayed {{ $review->booking->nights ?? 1 }} night(s) in {{ $review->stay_date?->format('F Y') ?? $review->booking->check_in->format('F Y') }}
                </p>
            @endif

            <!-- Review Content -->
            <div class="mt-3">
                @if($review->title)
                    <h5 class="font-medium mb-1">{{ $review->title }}</h5>
                @endif
                <p class="text-gray-700">{{ $review->review }}</p>
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
                        <img src="{{ asset($photo->photo_path ?? $photo->photo) }}" 
                             alt="Review photo"
                             class="w-20 h-20 rounded-lg object-cover cursor-pointer hover:opacity-80"
                             onclick="openPhotoModal('{{ asset($photo->photo_path ?? $photo->photo) }}')">
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
                    Helpful (<span class="helpful-count">{{ $review->helpful_votes_count ?? 0 }}</span>)
                </button>
                <button onclick="reportReview({{ $review->id }})" class="text-gray-500 hover:text-red-600">
                    <i class="fa-regular fa-flag mr-1"></i> Report
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
