@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Loyalty Reward</h5>
                    <a href="{{ route('admin.loyalty.rewards.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.loyalty.rewards.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reward Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required placeholder="Free Night Reward">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reward Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required id="reward-type">
                                    <option value="">Select Type</option>
                                    <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>Discount</option>
                                    <option value="free_night" {{ old('type') == 'free_night' ? 'selected' : '' }}>Free Night</option>
                                    <option value="upgrade" {{ old('type') == 'upgrade' ? 'selected' : '' }}>Room Upgrade</option>
                                    <option value="amenity" {{ old('type') == 'amenity' ? 'selected' : '' }}>Amenity</option>
                                    <option value="experience" {{ old('type') == 'experience' ? 'selected' : '' }}>Experience</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Points Required <span class="text-danger">*</span></label>
                                <input type="number" name="points_required" min="1" 
                                       class="form-control @error('points_required') is-invalid @enderror" 
                                       value="{{ old('points_required') }}" required>
                                @error('points_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3" id="discount-field" style="display: none;">
                                <label class="form-label">Discount Percentage</label>
                                <input type="number" name="discount_percentage" min="0" max="100" step="0.01" 
                                       class="form-control" value="{{ old('discount_percentage') }}">
                            </div>

                            <div class="col-md-6 mb-3" id="free-nights-field" style="display: none;">
                                <label class="form-label">Free Nights</label>
                                <input type="number" name="free_nights" min="1" 
                                       class="form-control" value="{{ old('free_nights', 1) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Tier</label>
                                <select name="min_tier_id" class="form-select">
                                    <option value="">Any Tier</option>
                                    @foreach($tiers as $tier)
                                    <option value="{{ $tier->id }}" {{ old('min_tier_id') == $tier->id ? 'selected' : '' }}>
                                        {{ $tier->name }} (Level {{ $tier->level }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid Days</label>
                                <input type="number" name="valid_days" min="1" class="form-control" 
                                       value="{{ old('valid_days') }}" placeholder="Days reward is valid after redemption">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Uses</label>
                                <input type="number" name="max_uses" min="1" class="form-control" 
                                       value="{{ old('max_uses') }}" placeholder="Leave empty for unlimited">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="Reward description">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Create Reward
                            </button>
                            <a href="{{ route('admin.loyalty.rewards.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('reward-type').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('discount-field').style.display = type === 'discount' ? 'block' : 'none';
    document.getElementById('free-nights-field').style.display = type === 'free_night' ? 'block' : 'none';
});
</script>

@endsection


