@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Coupons Management</h5>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Coupon
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Discount</th>
                                    <th>Min Amount</th>
                                    <th>Valid Period</th>
                                    <th>Uses</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $coupon->code }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $coupon->discount_type === 'percentage' ? 'bg-info' : 'bg-success' }}">
                                            {{ ucfirst($coupon->discount_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($coupon->discount_type === 'percentage')
                                            {{ $coupon->discount_value }}%
                                        @else
                                            ₹{{ number_format($coupon->discount_value) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->min_booking_amount)
                                            ₹{{ number_format($coupon->min_booking_amount) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->valid_from && $coupon->valid_until)
                                            {{ \Carbon\Carbon::parse($coupon->valid_from)->format('M d') }} - 
                                            {{ \Carbon\Carbon::parse($coupon->valid_until)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->usages_count ?? 0 }} / 
                                        {{ $coupon->max_uses ?? '∞' }}
                                    </td>
                                    <td>
                                        @if($coupon->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $coupon->is_active ? 'btn-warning' : 'btn-success' }}">
                                                    <i class='bx {{ $coupon->is_active ? 'bx-pause' : 'bx-play' }}'></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" 
                                                  class="d-inline" onsubmit="return confirm('Delete this coupon?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class='bx bx-gift text-muted' style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No coupons found.</p>
                                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mt-2">
                                            Create First Coupon
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


