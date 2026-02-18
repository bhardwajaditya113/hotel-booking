@extends('admin.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Property Verification Details</h4>
                    <a href="{{ route('admin.verification.properties.index') }}" class="btn btn-sm btn-secondary">← Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Property Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Property Name</th>
                                    <td>{{ $property->name }}</td>
                                </tr>
                                <tr>
                                    <th>Property Type</th>
                                    <td>{{ $property->type->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Listing Type</th>
                                    <td>
                                        <span class="badge {{ $property->listing_type === 'hotel' ? 'bg-primary' : 'bg-success' }}">
                                            {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $property->formatted_address }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $property->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $property->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{!! $property->description ?? 'N/A' !!}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $property->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($property->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Verification Status</th>
                                    <td>
                                        <span class="badge 
                                            {{ $property->verification_status === 'verified' ? 'bg-success' : 
                                               ($property->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ ucfirst($property->verification_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($property->verified_at)
                                <tr>
                                    <th>Verified At</th>
                                    <td>{{ $property->verified_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>

                            <h5 class="mb-3 mt-4">Host Information</h5>
                            @if($property->host)
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Host Name</th>
                                    <td>{{ $property->host->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $property->host->email }}</td>
                                </tr>
                                @if($property->host->hostProfile)
                                <tr>
                                    <th>Host Type</th>
                                    <td>{{ ucfirst($property->host->hostProfile->type) }}</td>
                                </tr>
                                <tr>
                                    <th>Host Verification</th>
                                    <td>
                                        <span class="badge 
                                            {{ $property->host->hostProfile->verification_status === 'verified' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($property->host->hostProfile->verification_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                            @endif

                            <h5 class="mb-3 mt-4">Rooms</h5>
                            <p>Total Rooms: {{ $property->activeRooms->count() }}</p>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Verification Action</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.verification.properties.verify', $property->id) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="verified" {{ $property->verification_status === 'verified' ? 'selected' : '' }}>
                                                    Verify
                                                </option>
                                                <option value="rejected" {{ $property->verification_status === 'rejected' ? 'selected' : '' }}>
                                                    Reject
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="4" 
                                                      placeholder="Add verification notes...">{{ old('notes', $property->verification_notes) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Update Verification</button>
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

