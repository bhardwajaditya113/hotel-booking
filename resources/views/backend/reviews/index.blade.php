@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Reviews Management</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
                           class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending ({{ $pendingCount ?? 0 }})
                        </a>
                        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" 
                           class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            Approved
                        </a>
                        <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" 
                           class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected
                        </a>
                    </div>
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
                                    <th>ID</th>
                                    <th>Guest</th>
                                    <th>Property/Room</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $review->user->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($review->room && $review->room->property)
                                            {{ $review->room->property->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $review->room->type->name ?? 'Room' }}</small>
                                        @elseif($review->room)
                                            <small class="text-muted">{{ $review->room->type->name ?? 'Room' }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class='bx bxs-star text-warning'></i>
                                            <strong class="ms-1">{{ number_format($review->rating_overall ?? $review->average_rating ?? 0, 1) }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            {{ \Illuminate\Support\Str::limit($review->comment ?? 'No comment', 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($review->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.reviews.show', $review->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            @if(!$review->is_approved)
                                            <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Approve this review?')">
                                                    <i class='bx bx-check'></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Reject this review?')">
                                                    <i class='bx bx-x'></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class='bx bx-info-circle text-muted' style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No reviews found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

