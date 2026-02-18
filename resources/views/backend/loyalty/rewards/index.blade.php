@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Loyalty Rewards Management</h5>
                    <a href="{{ route('admin.loyalty.rewards.create') }}" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Reward
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
                                    <th>Points Required</th>
                                    <th>Value</th>
                                    <th>Min Tier</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rewards as $reward)
                                <tr>
                                    <td>
                                        <strong>{{ $reward->name }}</strong>
                                        @if($reward->description)
                                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($reward->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $reward->type)) }}</span>
                                    </td>
                                    <td>{{ number_format($reward->points_required) }}</td>
                                    <td>
                                        @if($reward->type === 'discount')
                                            {{ $reward->discount_percentage ?? 0 }}% off
                                        @elseif($reward->type === 'free_night')
                                            {{ $reward->free_nights ?? 1 }} night(s)
                                        @else
                                            {{ ucfirst($reward->type) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($reward->minTier)
                                            {{ $reward->minTier->name }}
                                        @else
                                            <span class="text-muted">Any Tier</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reward->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.loyalty.rewards.edit', $reward->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.loyalty.rewards.destroy', $reward->id) }}" 
                                                  class="d-inline" onsubmit="return confirm('Delete this reward?')">
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
                                        <i class='bx bx-trophy text-muted' style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No rewards found.</p>
                                        <a href="{{ route('admin.loyalty.rewards.create') }}" class="btn btn-primary mt-2">
                                            Create First Reward
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $rewards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

