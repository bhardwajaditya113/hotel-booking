@extends('frontend.dashboard.account_master')

@section('account_title', __('frontend.account.title_bookings'))

@section('account_content')
    <div class="service-article-title">
        <h2>My bookings</h2>
    </div>
    <div class="service-article-content">
        <div class="billing-details border-0 shadow-none p-0">
            <h3 class="title mb-3">Booking list</h3>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
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
                                <td>
                                    <a href="{{ route('booking.confirmation', $item->id) }}" class="fw-semibold">{{ $item->code }}</a>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($item->property)
                                        <strong>{{ $item->property->name }}</strong><br>
                                        <small class="text-muted">{{ $item->property->city }}, {{ $item->property->country }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $item->room->type->name ?? 'Room' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ \Carbon\Carbon::parse($item->check_in)->format('d/m/Y') }}</span><br>
                                    <span class="badge bg-warning text-dark mt-1">{{ \Carbon\Carbon::parse($item->check_out)->format('d/m/Y') }}</span>
                                </td>
                                <td>{{ $item->number_of_rooms }}</td>
                                <td><strong>₹{{ number_format($item->total_price, 2) }}</strong></td>
                                <td>
                                    @if ($item->payment_status == 1)
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
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('user.invoice', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Download Invoice">
                                        <i class="bx bx-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bx bx-inbox" style="font-size: 2.5rem; color: var(--nx-border-strong);"></i>
                                    <p class="mt-2 text-muted mb-3">No bookings found</p>
                                    <a href="{{ url('/') }}" class="btn btn-primary">Browse properties</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
