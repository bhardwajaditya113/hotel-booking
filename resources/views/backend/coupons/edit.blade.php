@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="row">
        <div class="col-12">
            <div class="card radius-10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Coupon</h5>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $coupon->code) }}" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="discount_value" step="0.01" min="0" 
                                       class="form-control @error('discount_value') is-invalid @enderror" 
                                       value="{{ old('discount_value', $coupon->discount_value) }}" required>
                                @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Booking Amount</label>
                                <input type="number" name="min_booking_amount" step="0.01" min="0" 
                                       class="form-control" value="{{ old('min_booking_amount', $coupon->min_booking_amount) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Discount Amount</label>
                                <input type="number" name="max_discount_amount" step="0.01" min="0" 
                                       class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid From</label>
                                <input type="date" name="valid_from" class="form-control" 
                                       value="{{ old('valid_from', $coupon->valid_from ? \Carbon\Carbon::parse($coupon->valid_from)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" 
                                       value="{{ old('valid_until', $coupon->valid_until ? \Carbon\Carbon::parse($coupon->valid_until)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Uses</label>
                                <input type="number" name="max_uses" min="1" class="form-control" 
                                       value="{{ old('max_uses', $coupon->max_uses) }}" placeholder="Leave empty for unlimited">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Uses Per User</label>
                                <input type="number" name="max_uses_per_user" min="1" class="form-control" 
                                       value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $coupon->description) }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           id="is_active" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_public" value="1" 
                                           id="is_public" {{ old('is_public', $coupon->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Public (Visible to users)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update Coupon
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


