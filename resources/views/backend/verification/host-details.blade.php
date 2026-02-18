@extends('admin.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Host Verification Details</h4>
                    <a href="{{ route('admin.verification.hosts.index') }}" class="btn btn-sm btn-secondary">← Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Host Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Name</th>
                                    <td>{{ $host->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $host->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $host->user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Host Type</th>
                                    <td>
                                        <span class="badge 
                                            {{ $host->type === 'company' || $host->type === 'hotel_chain' ? 'bg-primary' : 'bg-info' }}">
                                            {{ ucfirst($host->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Display Name</th>
                                    <td>{{ $host->display_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Bio</th>
                                    <td>{{ $host->bio ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Verification Status</th>
                                    <td>
                                        <span class="badge 
                                            {{ $host->verification_status === 'verified' ? 'bg-success' : 
                                               ($host->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($host->verification_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($host->verified_at)
                                <tr>
                                    <th>Verified At</th>
                                    <td>{{ $host->verified_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>

                            @if($host->isCompany())
                            <h5 class="mb-3 mt-4">Company Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Company Name</th>
                                    <td>{{ $host->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Registration Number</th>
                                    <td>{{ $host->company_registration_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Tax ID</th>
                                    <td>{{ $host->company_tax_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Company Address</th>
                                    <td>{{ $host->company_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Website</th>
                                    <td>{{ $host->company_website ?? 'N/A' }}</td>
                                </tr>
                            </table>
                            @endif

                            <h5 class="mb-3 mt-4">Statistics</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Total Properties</th>
                                    <td>{{ $host->total_properties }}</td>
                                </tr>
                                <tr>
                                    <th>Total Bookings</th>
                                    <td>{{ $host->total_bookings }}</td>
                                </tr>
                                <tr>
                                    <th>Average Rating</th>
                                    <td>
                                        @if($host->average_rating)
                                            {{ number_format($host->average_rating, 1) }}/5 ({{ $host->total_reviews }} reviews)
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Response Rate</th>
                                    <td>{{ $host->response_rate ?? 'N/A' }}%</td>
                                </tr>
                                <tr>
                                    <th>Avg Response Time</th>
                                    <td>
                                        @if($host->average_response_time_minutes)
                                            {{ round($host->average_response_time_minutes / 60, 1) }} hours
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <h5 class="mb-3 mt-4">Properties</h5>
                            @if($host->properties && $host->properties->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>City</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($host->properties as $property)
                                        <tr>
                                            <td>{{ $property->name }}</td>
                                            <td>{{ $property->type->name ?? 'N/A' }}</td>
                                            <td>{{ $property->city }}</td>
                                            <td>
                                                <span class="badge {{ $property->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($property->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p>No properties found.</p>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Verification Action</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.verification.hosts.verify', $host->id) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="verified" {{ $host->verification_status === 'verified' ? 'selected' : '' }}>
                                                    Verify
                                                </option>
                                                <option value="rejected" {{ $host->verification_status === 'rejected' ? 'selected' : '' }}>
                                                    Reject
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="4" 
                                                      placeholder="Add verification notes...">{{ old('notes', $host->verification_notes) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-2">Update Verification</button>
                                    </form>

                                    @if($host->isVerified())
                                    <form method="POST" action="{{ route('admin.verification.hosts.toggle-superhost', $host->id) }}">
                                        @csrf
                                        <button type="submit" class="btn w-100 
                                            {{ $host->is_superhost ? 'btn-warning' : 'btn-success' }}">
                                            {{ $host->is_superhost ? 'Remove Superhost' : 'Grant Superhost' }}
                                        </button>
                                    </form>
                                    @endif
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

