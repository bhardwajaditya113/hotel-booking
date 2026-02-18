@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Loyalty Reward</h5>
                    <a href="{{ route('admin.loyalty.rewards.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.loyalty.rewards.update', $reward->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reward Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $reward->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Points Required <span class="text-danger">*</span></label>
                                <input type="number" name="points_required" min="1" 
                                       class="form-control @error('points_required') is-invalid @enderror" 
                                       value="{{ old('points_required', $reward->points_required) }}" required>
                                @error('points_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Percentage</label>
                                <input type="number" name="discount_percentage" min="0" max="100" step="0.01" 
                                       class="form-control" value="{{ old('discount_percentage', $reward->discount_percentage) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Free Nights</label>
                                <input type="number" name="free_nights" min="1" 
                                       class="form-control" value="{{ old('free_nights', $reward->free_nights) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Tier</label>
                                <select name="min_tier_id" class="form-select">
                                    <option value="">Any Tier</option>
                                    @foreach($tiers as $tier)
                                    <option value="{{ $tier->id }}" {{ old('min_tier_id', $reward->min_tier_id) == $tier->id ? 'selected' : '' }}>
                                        {{ $tier->name }} (Level {{ $tier->level }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $reward->description) }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $reward->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update Reward
                            </button>
                            <a href="{{ route('admin.loyalty.rewards.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


