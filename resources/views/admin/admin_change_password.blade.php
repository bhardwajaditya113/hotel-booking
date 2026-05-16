@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Change password</div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.password.update') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Current password</label>
                    <div class="col-sm-9">
                        <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('old_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">New password</label>
                    <div class="col-sm-9">
                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required autocomplete="new-password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Confirm</label>
                    <div class="col-sm-9">
                        <input type="password" name="new_password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary px-4">Update password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
