@extends('frontend.main_master')
@section('main')

<div class="inner-banner inner-bg9">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Confirm Password</li>
            </ul>
            <h3>Confirm Password</h3>
        </div>
    </div>
</div>

<div class="sign-in-area pt-100 pb-70">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="user-all-form">
                    <div class="contact-form">
                        <div class="section-title text-center">
                            <p>This is a secure area. Please confirm your password before continuing.</p>
                        </div>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="default-btn btn-bg-three border-radius-5">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
