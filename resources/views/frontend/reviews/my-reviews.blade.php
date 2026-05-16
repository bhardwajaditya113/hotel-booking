@extends('frontend.dashboard.account_master')

@section('account_title', __('frontend.account.my_reviews'))

@section('account_content')
    <div class="service-article-title">
        <h2>My reviews</h2>
    </div>
    <div class="service-article-content">
        @forelse($reviews as $review)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->room->type->name ?? 'Room #'.$review->room_id }}</strong>
                        <span class="text-muted small">{{ $review->created_at?->format('M j, Y') }}</span>
                    </div>
                    <p class="mb-1"><span class="badge bg-secondary">{{ $review->rating_overall }}/5</span></p>
                    <p class="mb-0 text-secondary">{{ \Illuminate\Support\Str::limit($review->review_text, 200) }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted">You have not submitted any reviews yet.</p>
        @endforelse
        <div class="mt-3">{{ $reviews->links() }}</div>
    </div>
@endsection
