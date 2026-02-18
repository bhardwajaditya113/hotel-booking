@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Coupon</h5>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.coupons.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" required placeholder="SUMMER2024">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Code will be converted to uppercase</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="discount_value" step="0.01" min="0" 
                                       class="form-control @error('discount_value') is-invalid @enderror" 
                                       value="{{ old('discount_value') }}" required>
                                @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Booking Amount</label>
                                <input type="number" name="min_booking_amount" step="0.01" min="0" 
                                       class="form-control" value="{{ old('min_booking_amount') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Discount Amount</label>
                                <input type="number" name="max_discount_amount" step="0.01" min="0" 
                                       class="form-control" value="{{ old('max_discount_amount') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid From</label>
                                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Uses</label>
                                <input type="number" name="max_uses" min="1" class="form-control" 
                                       value="{{ old('max_uses') }}" placeholder="Leave empty for unlimited">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Uses Per User</label>
                                <input type="number" name="max_uses_per_user" min="1" class="form-control" 
                                       value="{{ old('max_uses_per_user') }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="Optional description">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_public" value="1" 
                                           id="is_public" {{ old('is_public', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Public (Visible to users)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Create Coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


