@extends('frontend.main_master')
@section('main')

<div class="inner-banner inner-bg9">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Forgot Password</li>
            </ul>
            <h3>Forgot Password</h3>
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
                            <span class="sp-color">Reset</span>
                            <h2>Forgot your password?</h2>
                            <p class="mt-2">Enter your email and we will send you a reset link.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="default-btn btn-bg-three border-radius-5">Email Password Reset Link</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}">Back to sign in</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
