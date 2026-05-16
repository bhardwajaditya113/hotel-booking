@extends('frontend.main_master')
@section('main')

    <!-- Inner Banner -->
    <div class="inner-banner inner-bg10">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="{{ url('/') }}">Home</a>
                    </li>
                    <li><i class='bx bx-chevron-right' aria-hidden="true"></i></li>
                    <li aria-current="page">Create account</li>
                </ul>
                <h3>Create account</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Sign Up Area -->
    <div class="sign-up-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="user-all-form">
                        <div class="contact-form">
                            <div class="section-title text-center">
                                <span class="sp-color">{{ __('site.auth.join_brand', ['name' => config('app.name', 'Elapse')]) }}</span>
                                <h2>Create your account</h2>
                                <p class="small mt-2 mb-0">Save wishlists, message hosts, and book faster next time.</p>
                            </div>

    <form method="POST" action="{{ route('register') }}">
      @csrf

        <div class="row">
            <div class="col-lg-12 ">
                <div class="form-group">
                    <label class="form-label" for="name">Full name</label>
                    <input type="text" name="name" id="name" class="form-control" required autocomplete="name" placeholder="Your name">
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required autocomplete="email" placeholder="you@example.com">
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="At least 8 characters">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                </div>
            </div>

            <div class="col-lg-12 col-md-12 text-center">
                <button type="submit" class="default-btn btn-bg-three border-radius-5 w-100 w-md-auto">
                    Create account
                </button>
            </div>

            <div class="col-12">
                <p class="account-desc">
                    Already have an account? 
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>
        </div>
    </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sign Up Area End -->



@endsection