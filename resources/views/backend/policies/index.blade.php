@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cancellation Policies Management</h5>
                    <a href="{{ route('admin.policies.create') }}" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Policy
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
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Days Before Check-in</th>
                                    <th>Full Refund</th>
                                    <th>Partial Refund</th>
                                    <th>Free Cancellation</th>
                                    <th>Default</th>
                                    <th>Rooms Using</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                <tr>
                                    <td>
                                        <strong>{{ $policy->name }}</strong>
                                        @if($policy->is_default)
                                        <span class="badge bg-primary ms-2">Default</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $policy->slug }}</code></td>
                                    <td>{{ $policy->days_before_checkin }} days</td>
                                    <td>{{ $policy->full_refund_percentage }}%</td>
                                    <td>{{ $policy->partial_refund_percentage }}%</td>
                                    <td>
                                        @if($policy->is_free_cancellation)
                                        <span class="badge bg-success">Yes</span>
                                        @else
                                        <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($policy->is_default)
                                        <span class="badge bg-primary">Default</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $policy->rooms_count ?? 0 }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.policies.edit', $policy->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.policies.destroy', $policy->id) }}" 
                                                  class="d-inline" onsubmit="return confirm('Delete this policy?')">
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
                                    <td colspan="9" class="text-center py-4">
                                        <i class='bx bx-refresh text-muted' style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No cancellation policies found.</p>
                                        <a href="{{ route('admin.policies.create') }}" class="btn btn-primary mt-2">
                                            Create First Policy
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


