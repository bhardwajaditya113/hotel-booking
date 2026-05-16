@extends('frontend.main_master')

@section('main')
<div class="container py-5">
    <h1 class="h3 mb-2">Guest reviews</h1>
    <p class="text-muted mb-4">{{ $room->type->name ?? 'Room' }} · Overall {{ number_format($ratingBreakdown['overall'] ?? 0, 1) }} / 5 ({{ $ratingBreakdown['count'] ?? 0 }} reviews)</p>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4">
                <h2 class="h6 mb-3">Rating breakdown</h2>
                <ul class="list-unstyled small mb-0">
                    <li class="d-flex justify-content-between py-1"><span>Overall</span><strong>{{ number_format($ratingBreakdown['overall'] ?? 0, 1) }}</strong></li>
                    <li class="d-flex justify-content-between py-1"><span>Cleanliness</span><strong>{{ number_format($ratingBreakdown['cleanliness'] ?? 0, 1) }}</strong></li>
                    <li class="d-flex justify-content-between py-1"><span>Location</span><strong>{{ number_format($ratingBreakdown['location'] ?? 0, 1) }}</strong></li>
                    <li class="d-flex justify-content-between py-1"><span>Value</span><strong>{{ number_format($ratingBreakdown['value'] ?? 0, 1) }}</strong></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-8">
            @forelse($reviews as $review)
                <div class="card border-0 shadow-sm mb-3 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>{{ $review->user->name ?? 'Guest' }}</strong>
                        <span class="badge bg-secondary">{{ $review->rating_overall }}/5</span>
                    </div>
                    <p class="mb-0 text-secondary">{{ $review->review_text }}</p>
                </div>
            @empty
                <p class="text-muted">No reviews yet for this room.</p>
            @endforelse
            <div class="mt-3">{{ $reviews->links() }}</div>
        </div>
    </div>
</div>
@endsection
