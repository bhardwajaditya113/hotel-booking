@foreach($properties as $property)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 border shadow-sm">
            <div class="card-body">
                <h3 class="h6 mb-2">{{ $property->name }}</h3>
                <p class="small text-muted mb-2">{{ $property->city }}, {{ $property->country }}</p>
                <a href="{{ url('/property/'.$property->id.'/view') }}" class="stretched-link text-decoration-none">{{ __('frontend.search.property_view_listing') }}</a>
            </div>
        </div>
    </div>
@endforeach
