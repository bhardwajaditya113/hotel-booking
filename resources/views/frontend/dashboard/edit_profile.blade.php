@extends('frontend.dashboard.account_master')

@section('account_title', __('frontend.account.title_profile'))

@section('account_content')
    <div class="service-article-title">
        <h2>Profile & settings</h2>
    </div>
    <div class="service-article-content">
        <form action="{{ route('profile.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="billing-details border-0 shadow-none p-0">
                <h3 class="title">Your details</h3>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $profileData->name }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $profileData->email }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label>Address <span class="required">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ $profileData->address }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label>Phone <span class="required">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $profileData->phone }}">
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label for="image">Profile photo</label>
                            <input type="file" name="photo" class="form-control" id="image" accept="image/*">
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="d-block">Preview</label>
                            <img id="showImage"
                                 src="{{ !empty($profileData->photo) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}"
                                 alt=""
                                 class="rounded-circle border"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('nexstay-page-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var input = document.getElementById('image');
                var img = document.getElementById('showImage');
                if (!input || !img) return;
                input.addEventListener('change', function (e) {
                    var file = e.target.files && e.target.files[0];
                    if (!file) return;
                    var reader = new FileReader();
                    reader.onload = function (ev) {
                        img.setAttribute('src', ev.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            });
        </script>
    @endpush
@endsection
