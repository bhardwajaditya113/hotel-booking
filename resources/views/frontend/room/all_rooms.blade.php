@extends('frontend.main_master')
@section('main')

  <!-- Inner Banner -->
  <div class="inner-banner inner-bg9">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Rooms</li>
            </ul>
            <h3>Rooms</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Room Area -->
<div class="room-area pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <span class="sp-color">ROOMS</span>
            <h2>Our Rooms & Rates</h2>
        </div>
        <div class="row pt-45">
           
           @foreach ($rooms as $item)
            <div class="col-lg-4 col-md-6">
                <div class="room-card">
                    <a href="{{ url('room/details/'.$item->id) }}">
                        <img src="{{ asset( 'upload/roomimg/'.$item->image ) }}" alt="Images" style="width: 550px; height:300px;">
                    </a>
                    <div class="content">
                        <h6><a href="{{ url('room/details/'.$item->id) }}">{{ $item['type']['name'] }}</a></h6>
                        @if($item->property)
                        <div class="mb-1 text-xs text-gray-600">
                            <span class="badge bg-info">{{ $item->property->type->name ?? 'Property' }}</span>
                            <span class="ms-2"><i class="bx bx-home"></i> {{ $item->property->name }}</span>
                            <span class="ms-2"><i class="bx bx-map"></i> {{ $item->property->city }}</span>
                        </div>
                        @endif
                        <p class="mb-2">{{ Str::limit($item->short_desc, 80) }}</p>
                        <ul>
                            <li class="text-color">${{ $item->price }}</li>
                            <li class="text-color">Per Night</li>
                            @if($item->discount > 0)
                            <li class="text-danger"><s>${{ round($item->price / (1 - $item->discount/100)) }}</s></li>
                            @endif
                        </ul>
                        <div class="rating text-color mb-2">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star-half'></i>
                        </div>
                        <div class="room-info mb-3">
                            <span class="badge bg-secondary me-1"><i class='bx bx-user'></i> {{ $item->room_capacity }} Guests</span>
                            <span class="badge bg-secondary me-1"><i class='bx bx-expand'></i> {{ $item->size }}ft²</span>
                            <span class="badge bg-secondary"><i class='bx bxs-bed'></i> {{ $item->bed_style }}</span>
                        </div>
                        <a href="{{ url('room/details/'.$item->id) }}" class="default-btn btn-bg-one border-radius-5 w-100">
                            <i class='bx bx-calendar-check'></i> Book Now
                        </a>
                    </div>
                </div>
            </div> 
           @endforeach

        
        </div>
    </div>
</div>
<!-- Room Area End -->






@endsection