@extends('frontend.main_master')
@section('main')
<section class="page-header py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="fw-bold mb-2">{{ __('frontend.search.results_heading') }}</h1>
                <p class="mb-0 text-muted">{{ __('frontend.search.results_sub') }}</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        @forelse($rooms as $room)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($room->image_url ?? false)
                        <a href="{{ url('/room/details/'.$room->id) }}">
                            <img src="{{ $room->image_url }}" class="card-img-top" alt="" style="height: 180px; object-fit: cover;">
                        </a>
                    @endif
                    <div class="card-body">
                        <h2 class="h5 card-title">
                            <a href="{{ url('/room/details/'.$room->id) }}" class="text-decoration-none text-dark">{{ $room->type->name ?? __('frontend.search.results_room_fallback') }}</a>
                        </h2>
                        <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($room->short_desc ?? $room->description ?? '', 100) }}</p>
                        <p class="fw-bold mb-0">{{ $room->formatted_price ?? ('₹'.number_format($room->price ?? 0)) }} <span class="text-muted small fw-normal">{{ __('frontend.search.results_per_night') }}</span></p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('frontend.search.results_empty') }}</p>
            </div>
        @endforelse
    </div>
    <div class="mt-4 d-flex justify-content-center">
        {{ $rooms->withQueryString()->links() }}
    </div>
</div>
@endsection
