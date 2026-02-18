@extends('frontend.main_master')
@section('main')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

  <!-- Inner Banner -->
  <div class="inner-banner inner-bg6">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="index.html">Home</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>User Booking List </li>
            </ul>
            <h3>User Booking List</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Service Details Area -->
<div class="service-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
             <div class="col-lg-3">

                @include('frontend.dashboard.user_menu')

            </div>


            <div class="col-lg-9">
                <div class="service-article">
                    

    <section class="checkout-area pb-70">
    <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="billing-details">
                        <h3 class="title">User Booking List  </h3>

    

    <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">B No</th>
            <th scope="col">B Date</th>
            <th scope="col">Property</th>
            <th scope="col">Room</th>
            <th scope="col">Check In/Out</th>
            <th scope="col">Total Room</th>
            <th scope="col">Amount</th>
            <th scope="col">Payment</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allData as $item) 
            <tr>
            <td> <a href="{{ route('booking.confirmation', $item->id) }}" class="text-primary">{{ $item->code }}</a> </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
            <td>
                @if($item->property)
                    <strong>{{ $item->property->name }}</strong><br>
                    <small class="text-muted">{{ $item->property->city }}, {{ $item->property->country }}</small>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
            <td>{{ $item->room->type->name ?? 'Room' }}</td>
            <td> 
                <span class="badge bg-primary">{{ \Carbon\Carbon::parse($item->check_in)->format('d/m/Y') }}</span><br>
                <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($item->check_out)->format('d/m/Y') }}</span>
            </td>
            <td>{{ $item->number_of_rooms }}</td>
            <td>
                <strong>₹{{ number_format($item->total_price, 2) }}</strong>
            </td>
            <td>
                @if($item->payment_status == 1)
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-danger">Pending</span>
                @endif
            </td>
            <td> 
                @if ($item->status == 1)
                <span class="badge bg-success">Complete</span>
                   @else
                   <span class="badge bg-info text-dark">Pending</span>
                @endif
            </td>
            <td>
                <a href="{{ route('booking.confirmation', $item->id) }}" class="btn btn-sm btn-primary" title="View Details">
                    <i class='bx bx-show'></i>
                </a>
                <a href="{{ route('user.invoice', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Download Invoice">
                    <i class='bx bx-download'></i>
                </a>
            </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class='bx bx-inbox' style="font-size: 48px; color: #ccc;"></i>
                    <p class="mt-2 text-muted">No bookings found</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Browse Properties</a>
                </td>
            </tr>
            @endforelse
            
        </tbody>
        </table>



</div>
</div>
</div>
        
    </div>
</section>
                    
                </div>
            </div>

           
        </div>
    </div>
</div>
<!-- Service Details Area End -->

 


@endsection