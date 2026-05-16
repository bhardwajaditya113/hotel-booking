@extends('frontend.main_master')
@section('main')


      <!-- Inner Banner -->
      <div class="inner-banner inner-bg9">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="{{ url('/') }}">Home</a>
                    </li>
                    <li><i class='bx bx-chevron-right' aria-hidden="true"></i></li>
                    <li aria-current="page">Sign in</li>
                </ul>
                <h3>Sign in</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Sign In Area -->
    <div class="sign-in-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="user-all-form">
                        <div class="contact-form">
                            <div class="section-title text-center">
                                <span class="sp-color">Welcome back</span>
                                <h2>Sign in to continue</h2>
                                <p class="small mt-2 mb-0">Use the email, username, or phone you registered with.</p>
                            </div>
                           
                           
                 <form method="POST" action="{{ route('login') }}">
                 @csrf

        <div class="row">
            <div class="col-lg-12 ">
                <div class="form-group">
                    <label class="form-label" for="login">Email, username, or phone</label>
                    <input type="text" name="login" id="login" class="form-control" required autocomplete="username" placeholder="you@example.com">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>
            </div>

                                    <div class="col-lg-6 col-sm-6 form-condition">
                                        <div class="agree-label form-check">
                                            <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
                                            <label class="form-check-label" for="remember">
                                                Keep me signed in on this device
                                            </label>
                                        </div>
                                    </div>
        
        <div class="col-lg-6 col-sm-6 text-lg-end">
            <a class="forget" href="{{ route('password.request') }}">Forgot password?</a>
        </div>
    
                                    <div class="col-lg-12 col-md-12 text-center">
          <button type="submit" class="default-btn btn-bg-three border-radius-5 w-100 w-md-auto">
                                            Sign in
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <p class="account-desc">
                                            New here?
                                            <a href="{{ route('register') }}">Create an account</a>
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
    <!-- Sign In Area End -->

@endsection