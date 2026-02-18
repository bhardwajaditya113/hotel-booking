@extends('admin.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Property Verification</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.verification.properties.index', ['status' => 'pending']) }}" 
                           class="btn btn-sm {{ request('status') == 'pending' || !request('status') ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending
                        </a>
                        <a href="{{ route('admin.verification.properties.index', ['status' => 'verified']) }}" 
                           class="btn btn-sm {{ request('status') == 'verified' ? 'btn-success' : 'btn-outline-success' }}">
                            Verified
                        </a>
                        <a href="{{ route('admin.verification.properties.index', ['status' => 'rejected']) }}" 
                           class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.verification.properties.bulk-verify') }}" id="bulk-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Property Name</th>
                                        <th>Type</th>
                                        <th>Listing Type</th>
                                        <th>Host</th>
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Verification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($properties as $property)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="property-checkbox">
                                        </td>
                                        <td>{{ $property->name }}</td>
                                        <td>{{ $property->type->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $property->listing_type === 'hotel' ? 'bg-primary' : 'bg-success' }}">
                                                {{ $property->listing_type === 'hotel' ? 'Hotel' : 'Unique Stay' }}
                                            </span>
                                        </td>
                                        <td>{{ $property->host->name ?? 'N/A' }}</td>
                                        <td>{{ $property->city }}</td>
                                        <td>
                                            <span class="badge {{ $property->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($property->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                {{ $property->verification_status === 'verified' ? 'bg-success' : 
                                                   ($property->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                {{ ucfirst($property->verification_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.verification.properties.show', $property->id) }}" 
                                               class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No properties found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($properties->count() > 0 && (request('status') == 'pending' || !request('status')))
                        <div class="mt-3 d-flex gap-2">
                            <select name="status" class="form-control" style="width: auto;" required>
                                <option value="">Select Action</option>
                                <option value="verified">Verify Selected</option>
                                <option value="rejected">Reject Selected</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                        @endif
                    </form>

                    <div class="mt-4">
                        {{ $properties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.property-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection

