@extends('admin.admin_dashboard')
@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Host Verification</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.verification.hosts.index', ['status' => 'pending']) }}" 
                           class="btn btn-sm {{ request('status') == 'pending' || !request('status') ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending
                        </a>
                        <a href="{{ route('admin.verification.hosts.index', ['status' => 'verified']) }}" 
                           class="btn btn-sm {{ request('status') == 'verified' ? 'btn-success' : 'btn-outline-success' }}">
                            Verified
                        </a>
                        <a href="{{ route('admin.verification.hosts.index', ['status' => 'rejected']) }}" 
                           class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Rejected
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.verification.hosts.bulk-verify') }}" id="bulk-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Host Name</th>
                                        <th>Type</th>
                                        <th>Email</th>
                                        <th>Properties</th>
                                        <th>Rating</th>
                                        <th>Verification</th>
                                        <th>Superhost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hosts as $host)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="host_ids[]" value="{{ $host->id }}" class="host-checkbox">
                                        </td>
                                        <td>{{ $host->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $host->type === 'company' || $host->type === 'hotel_chain' ? 'bg-primary' : 'bg-info' }}">
                                                {{ ucfirst($host->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $host->user->email ?? 'N/A' }}</td>
                                        <td>{{ $host->total_properties }}</td>
                                        <td>
                                            @if($host->average_rating)
                                                {{ number_format($host->average_rating, 1) }}/5
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                {{ $host->verification_status === 'verified' ? 'bg-success' : 
                                                   ($host->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                                {{ ucfirst($host->verification_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($host->is_superhost)
                                            <span class="badge bg-purple">⭐ Superhost</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.verification.hosts.show', $host->id) }}" 
                                               class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hosts found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($hosts->count() > 0 && (request('status') == 'pending' || !request('status')))
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
                        {{ $hosts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.host-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection

