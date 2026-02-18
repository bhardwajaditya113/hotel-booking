@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Pricing Rule</h5>
                    <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.pricing.update', $rule->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $rule->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rule Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="weekend" {{ old('type', $rule->type) == 'weekend' ? 'selected' : '' }}>Weekend</option>
                                    <option value="seasonal" {{ old('type', $rule->type) == 'seasonal' ? 'selected' : '' }}>Seasonal</option>
                                    <option value="last_minute" {{ old('type', $rule->type) == 'last_minute' ? 'selected' : '' }}>Last Minute</option>
                                    <option value="early_bird" {{ old('type', $rule->type) == 'early_bird' ? 'selected' : '' }}>Early Bird</option>
                                    <option value="event" {{ old('type', $rule->type) == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="occupancy" {{ old('type', $rule->type) == 'occupancy' ? 'selected' : '' }}>Occupancy Based</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                                <select name="adjustment_type" class="form-select @error('adjustment_type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('adjustment_type', $rule->adjustment_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('adjustment_type', $rule->adjustment_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('adjustment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adjustment Value <span class="text-danger">*</span></label>
                                <input type="number" name="adjustment_value" step="0.01" 
                                       class="form-control @error('adjustment_value') is-invalid @enderror" 
                                       value="{{ old('adjustment_value', $rule->adjustment_value) }}" required>
                                @error('adjustment_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ old('start_date', $rule->start_date ? \Carbon\Carbon::parse($rule->start_date)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ old('end_date', $rule->end_date ? \Carbon\Carbon::parse($rule->end_date)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Days Before Check-in</label>
                                <input type="number" name="min_days_before" min="0" class="form-control" 
                                       value="{{ old('min_days_before', $rule->min_days_before) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Days Before Check-in</label>
                                <input type="number" name="max_days_before" min="0" class="form-control" 
                                       value="{{ old('max_days_before', $rule->max_days_before) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <input type="number" name="priority" min="0" 
                                       class="form-control @error('priority') is-invalid @enderror" 
                                       value="{{ old('priority', $rule->priority) }}" required>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update Pricing Rule
                            </button>
                            <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


