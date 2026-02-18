@extends('admin.admin_dashboard')
@section('admin') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
  $bookings = App\Models\Booking::latest()->get();
  $pending = App\Models\Booking::where('status','0')->get();
  $complete = App\Models\Booking::where('status','1')->get();
  $totalPrice = App\Models\Booking::sum('total_price');

  $today = Carbon\Carbon::now()->toDateString();
  $todayprice = App\Models\Booking::whereDate('created_at',$today)->sum('total_price');

  $allData = App\Models\Booking::orderBy('id','desc')->limit(10)->get();

  // Property Statistics
  $totalProperties = App\Models\Property::count();
  $pendingProperties = App\Models\Property::where('verification_status', 'pending')->count();
  $verifiedProperties = App\Models\Property::where('verification_status', 'verified')->count();
  $hotelProperties = App\Models\Property::where('listing_type', 'hotel')->count();
  $uniqueStayProperties = App\Models\Property::where('listing_type', 'unique_stay')->count();

  // Host Statistics
  $totalHosts = App\Models\HostProfile::count();
  $pendingHosts = App\Models\HostProfile::where('verification_status', 'pending')->count();
  $verifiedHosts = App\Models\HostProfile::where('verification_status', 'verified')->count();
  $superhosts = App\Models\HostProfile::where('is_superhost', true)->count();

  // Review Statistics
  $totalReviews = App\Models\Review::count();
  $pendingReviews = App\Models\Review::where('is_approved', false)->count();
  $approvedReviews = App\Models\Review::where('is_approved', true)->count();

@endphp

<div class="page-content">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
       <div class="col">
         <div class="card radius-10 border-start border-0 border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Booking</p>
                        <h4 class="my-1 text-info">{{ count($bookings) }}</h4>
                        <p class="mb-0 font-13">Today Sale:  ${{ $todayprice }}</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class='bx bxs-cart'></i>
                    </div>
                </div>
            </div>
         </div>
       </div>
       <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-danger">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Pening Booking</p>
                       <h4 class="my-1 text-danger">{{ count($pending) }}</h4>
                       <p class="mb-0 font-13">+5.4% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class='bx bxs-wallet'></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Complete Booking</p>
                       <h4 class="my-1 text-success">{{ count($complete) }}</h4>
                       <p class="mb-0 font-13">-4.5% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='bx bxs-bar-chart-alt-2' ></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Total Price</p>
                       <h4 class="my-1 text-warning">${{ $totalPrice  }}</h4>
                       <p class="mb-0 font-13">+8.4% from last week</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class='bx bxs-group'></i>
                   </div>
               </div>
           </div>
        </div>
      </div> 
    </div><!--end row-->

    <!-- Property & Host Statistics -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mt-3">
       <div class="col">
         <div class="card radius-10 border-start border-0 border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total Properties</p>
                        <h4 class="my-1 text-primary">{{ $totalProperties }}</h4>
                        <p class="mb-0 font-13">Hotels: {{ $hotelProperties }} | Unique: {{ $uniqueStayProperties }}</p>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-primary text-white ms-auto"><i class='bx bx-building-house'></i>
                    </div>
                </div>
            </div>
         </div>
       </div>
       <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-warning">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Pending Verification</p>
                       <h4 class="my-1 text-warning">{{ $pendingProperties }}</h4>
                       <p class="mb-0 font-13">Properties awaiting review</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-warning text-white ms-auto"><i class='bx bx-time-five'></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-success">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Verified Properties</p>
                       <h4 class="my-1 text-success">{{ $verifiedProperties }}</h4>
                       <p class="mb-0 font-13">Active & verified</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-success text-white ms-auto"><i class='bx bx-check-circle'></i>
                   </div>
               </div>
           </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-4 border-info">
           <div class="card-body">
               <div class="d-flex align-items-center">
                   <div>
                       <p class="mb-0 text-secondary">Total Hosts</p>
                       <h4 class="my-1 text-info">{{ $totalHosts }}</h4>
                       <p class="mb-0 font-13">Superhosts: {{ $superhosts }}</p>
                   </div>
                   <div class="widgets-icons-2 rounded-circle bg-gradient-info text-white ms-auto"><i class='bx bx-user-circle'></i>
                   </div>
               </div>
           </div>
        </div>
      </div> 
    </div><!--end row-->

    <!-- Quick Actions -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.verification.properties.index') }}" class="btn btn-outline-primary w-100">
                                <i class='bx bx-check-square me-1'></i> Verify Properties
                                @if($pendingProperties > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingProperties }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.verification.hosts.index') }}" class="btn btn-outline-success w-100">
                                <i class='bx bx-user-check me-1'></i> Verify Hosts
                                @if($pendingHosts > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingHosts }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-warning w-100">
                                <i class='bx bx-star me-1'></i> Manage Reviews
                                @if($pendingReviews > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingReviews }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-info w-100">
                                <i class='bx bx-gift me-1'></i> Manage Coupons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-12 col-lg-12 d-flex">
          <div class="card radius-10 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Sales Overview</h6>
                    </div>
                     
                </div>
            </div>
            
              <div class="row row-cols-1 row-cols-md-3 row-cols-xl-3 g-0 row-group text-center border-top">
                 
               <canvas id="bookingChart"></canvas>
               
              </div>
          </div>
       </div>
       



    </div><!--end row-->

     <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Recent Booking</h6>
                </div>
                
            </div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
              <table class="table table-striped table-bordered" style="width:100%">
                  <thead>
                      <tr>
                          <th>Sl</th>
                          <th>B No</th>
                          <th>B Date</th>
                          <th>Customer</th>
                          <th>Room</th>
                          <th>Check IN/Out</th>
                          <th>Total Room</th>
                          <th>Guest</th> 
                      </tr>
                  </thead>
                  <tbody>
                     @foreach ($allData as $key=> $item ) 
                      <tr>
                          <td>{{ $key+1 }}</td>
                          <td> <a href="{{ route('edit_booking',$item->id) }}"> {{ $item->code }} </a></td>
                          <td> {{ $item->created_at->format('d/m/Y') }} </td>
                          <td> {{ $item['user']['name'] }} </td>
                          <td> {{ $item['room']['type']['name'] }} </td>
                          <td> <span class="badge bg-primary">{{ $item->check_in }}</span>   <span class="badge bg-warning text-dark">{{ $item->check_out }}</span> </td>
                          <td> {{ $item->number_of_rooms }} </td>
                          <td> {{ $item->persion }} </td>
                    
                           
                      </tr>
                      @endforeach 
                    
                  </tbody>
               
              </table>
          </div>
      </div>


        </div>
 
         
</div>


<script>
  var ctx = document.getElementById('bookingChart').getContext('2d');
  var bookings = @json($bookings);

  // Extract the required data from the bookings
  var labels = bookings.map(function(booking) {
      return booking.check_in; 
  });

  var data = bookings.map(function(booking) {
      return booking.total_price;
  });

  var bookingChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: labels,
          datasets: [{
              label: 'Booking Data',
              data: data,
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1
          }]
      },
      options: {
          scales: {
              y: {
                  beginAtZero: true
              }
          }
      }
  });
</script>

@endsection