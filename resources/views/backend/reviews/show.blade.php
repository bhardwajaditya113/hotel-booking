@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Review Details</h5>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-3">Review Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Guest Name</th>
                                    <td>{{ $review->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $review->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Property/Room</th>
                                    <td>
                                        @if($review->room && $review->room->property)
                                            {{ $review->room->property->name ?? 'N/A' }} - 
                                            {{ $review->room->type->name ?? 'Room' }}
                                        @elseif($review->room)
                                            {{ $review->room->type->name ?? 'Room' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Overall Rating</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $rating = $review->rating_overall ?? $review->average_rating ?? 0;
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class='bx {{ $i <= $rating ? 'bxs-star' : 'bx-star' }} text-warning'></i>
                                            @endfor
                                            <span class="ms-2 fw-bold">{{ number_format($rating, 1) }}/5</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Comment</th>
                                    <td>{{ $review->comment ?? 'No comment provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($review->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Review Date</th>
                                    <td>{{ $review->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>

                            @if($review->photos && $review->photos->count() > 0)
                            <h6 class="mb-3 mt-4">Review Photos</h6>
                            <div class="row g-2">
                                @foreach($review->photos as $photo)
                                <div class="col-md-3">
                                    <img src="{{ asset($photo->image_path) }}" alt="Review Photo" 
                                         class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    @if(!$review->is_approved)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="mb-3">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class='bx bx-check'></i> Approve Review
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.reviews.reject', $review->id) }}" class="mb-3">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label">Rejection Reason</label>
                                            <textarea name="reason" class="form-control" rows="3" 
                                                      placeholder="Optional reason for rejection"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class='bx bx-x'></i> Reject Review
                                        </button>
                                    </form>
                                    @else
                                    <div class="alert alert-info">
                                        This review has been approved.
                                    </div>
                                    @endif

                                    <hr>

                                    <h6 class="mb-2">Add Response</h6>
                                    <form method="POST" action="{{ route('admin.reviews.respond', $review->id) }}">
                                        @csrf
                                        <div class="mb-2">
                                            <textarea name="response" class="form-control" rows="4" 
                                                      placeholder="Add a response to this review...">{{ old('response', $review->manager_response) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class='bx bx-message'></i> Add Response
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

