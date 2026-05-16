@foreach($rooms as $room)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border shadow-sm">
            @if($room->image_url ?? false)
                <a href="{{ url('/room/details/'.$room->id) }}">
                    <img src="{{ $room->image_url }}" class="card-img-top" alt="" style="height: 160px; object-fit: cover;">
                </a>
            @endif
            <div class="card-body">
                <h3 class="h6 mb-2"><a href="{{ url('/room/details/'.$room->id) }}" class="text-dark text-decoration-none">{{ $room->type->name ?? __('frontend.search.results_room_fallback') }}</a></h3>
                <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($room->short_desc ?? '', 80) }}</p>
                <p class="fw-semibold mb-0">{{ $room->formatted_price ?? ('₹'.number_format($room->price ?? 0)) }} <span class="text-muted small">{{ __('frontend.search.results_per_night') }}</span></p>
            </div>
        </div>
    </div>
@endforeach
