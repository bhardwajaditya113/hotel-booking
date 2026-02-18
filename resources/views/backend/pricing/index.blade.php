@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pricing Rules Management</h5>
                    <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Pricing Rule
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
                                    <th>Type</th>
                                    <th>Adjustment</th>
                                    <th>Period</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                <tr>
                                    <td><strong>{{ $rule->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rule->type)) }}</span>
                                    </td>
                                    <td>
                                        @if($rule->adjustment_type === 'percentage')
                                            {{ $rule->adjustment_value > 0 ? '+' : '' }}{{ $rule->adjustment_value }}%
                                        @else
                                            {{ $rule->adjustment_value > 0 ? '+' : '' }}₹{{ number_format($rule->adjustment_value) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($rule->start_date && $rule->end_date)
                                            {{ \Carbon\Carbon::parse($rule->start_date)->format('M d') }} - 
                                            {{ \Carbon\Carbon::parse($rule->end_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Always</span>
                                        @endif
                                    </td>
                                    <td>{{ $rule->priority }}</td>
                                    <td>
                                        @if($rule->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.pricing.edit', $rule->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.pricing.toggle', $rule->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $rule->is_active ? 'btn-warning' : 'btn-success' }}">
                                                    <i class='bx {{ $rule->is_active ? 'bx-pause' : 'bx-play' }}'></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.pricing.destroy', $rule->id) }}" 
                                                  class="d-inline" onsubmit="return confirm('Delete this pricing rule?')">
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
                                    <td colspan="7" class="text-center py-4">
                                        <i class='bx bx-dollar-circle text-muted' style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No pricing rules found.</p>
                                        <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary mt-2">
                                            Create First Pricing Rule
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $rules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


