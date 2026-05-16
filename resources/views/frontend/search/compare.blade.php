@extends('frontend.main_master')
@section('main')
<div class="container py-5">
    <h1 class="h3 mb-4">{{ __('frontend.search.compare_heading') }}</h1>
    <div class="table-responsive bg-white shadow-sm rounded">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th></th>
                    @foreach($rooms as $room)
                        <th>{{ $room->type->name ?? __('frontend.search.compare_room_number', ['id' => $room->id]) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr><th>{{ __('frontend.search.compare_price_night') }}</th>@foreach($rooms as $room)<td>{{ $room->formatted_price ?? ('₹'.number_format($room->price ?? 0)) }}</td>@endforeach</tr>
                <tr><th>{{ __('frontend.search.compare_capacity') }}</th>@foreach($rooms as $room)<td>@php $cap = $room->room_capacity; @endphp @if($cap === null || $cap === '')—@else{{ $cap }} {{ __('frontend.search.compare_guests_suffix') }}@endif</td>@endforeach</tr>
                <tr><th>{{ __('frontend.search.compare_bed') }}</th>@foreach($rooms as $room)<td>{{ $room->bed_style ?? '—' }}</td>@endforeach</tr>
            </tbody>
        </table>
    </div>
    <a href="{{ route('search.results') }}" class="btn btn-outline-secondary mt-4">{{ __('frontend.search.compare_back') }}</a>
</div>
@endsection
