@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Cancellation Policy</h5>
                    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.policies.update', $policy->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Policy Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $policy->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $policy->slug) }}" required>
                                @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $policy->description) }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Days Before Check-in <span class="text-danger">*</span></label>
                                <input type="number" name="days_before_checkin" min="0" 
                                       class="form-control @error('days_before_checkin') is-invalid @enderror" 
                                       value="{{ old('days_before_checkin', $policy->days_before_checkin) }}" required>
                                @error('days_before_checkin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Refund Percentage <span class="text-danger">*</span></label>
                                <input type="number" name="full_refund_percentage" min="0" max="100" step="0.01" 
                                       class="form-control @error('full_refund_percentage') is-invalid @enderror" 
                                       value="{{ old('full_refund_percentage', $policy->full_refund_percentage) }}" required>
                                @error('full_refund_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Partial Refund Percentage <span class="text-danger">*</span></label>
                                <input type="number" name="partial_refund_percentage" min="0" max="100" step="0.01" 
                                       class="form-control @error('partial_refund_percentage') is-invalid @enderror" 
                                       value="{{ old('partial_refund_percentage', $policy->partial_refund_percentage) }}" required>
                                @error('partial_refund_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Days Before Partial Refund <span class="text-danger">*</span></label>
                                <input type="number" name="days_before_partial_refund" min="0" 
                                       class="form-control @error('days_before_partial_refund') is-invalid @enderror" 
                                       value="{{ old('days_before_partial_refund', $policy->days_before_partial_refund) }}" required>
                                @error('days_before_partial_refund')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hours Before Full Charge <span class="text-danger">*</span></label>
                                <input type="number" name="hours_before_full_charge" min="0" 
                                       class="form-control @error('hours_before_full_charge') is-invalid @enderror" 
                                       value="{{ old('hours_before_full_charge', $policy->hours_before_full_charge) }}" required>
                                @error('hours_before_full_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_free_cancellation" value="1" 
                                           id="is_free_cancellation" {{ old('is_free_cancellation', $policy->is_free_cancellation) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_free_cancellation">Free Cancellation</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" 
                                           id="is_default" {{ old('is_default', $policy->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">Set as Default Policy</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update Policy
                            </button>
                            <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


