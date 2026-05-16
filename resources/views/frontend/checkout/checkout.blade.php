@extends('frontend.main_master')
@section('main')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
 <!-- Inner Banner -->
 <div class="inner-banner inner-bg7">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>{{ __('frontend.checkout.breadcrumb') }}</li>
            </ul>
            <h3>{{ __('frontend.checkout.page_title') }}</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Checkout Area -->
<section class="checkout-area pt-100 pb-70">
    <div class="container">

        <form method="post" role="form" action="{{ route('checkout.store') }}" class="checkout-form" id="checkout-form">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="billing-details">
                        <h3 class="title">{{ __('frontend.checkout.billing_details') }}</h3>

                        <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_country') }} <span class="required">*</span></label>
                <div class="select-box">
                    <select name="country" class="form-control">
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="India">India</option>
                        <option value="United Arab">United Arab Emirates</option>
                        <option value="China">China</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Germany">Germany</option>
                        <option value="France">France</option>
                        <option value="Japan">Japan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_name') }} <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ \Auth::user()->name }}">
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_email') }} <span class="required">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ \Auth::user()->email }}">
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_phone') }} <span class="required">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ \Auth::user()->phone }}" required>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_address') }} <span class="required">*</span></label>
                <input type="text" name="address" class="form-control" value="{{ \Auth::user()->address }}">
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_state') }} <span class="required">*</span></label>
                <input type="text" name="state" class="form-control">
                @if ($errors->has('state'))
                    <div class="text-danger">{{ $errors->first('state') }}</div>
                @endif
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="form-group">
                <label>{{ __('frontend.checkout.label_zip') }} <span class="required">*</span></label>
                <input type="text" name="zip_code" class="form-control">
                @if ($errors->has('zip_code'))
                    <div class="text-danger">{{ $errors->first('zip_code') }}</div>
                @endif
            </div>
        </div>


    {{-- <p>Session Value : {{ json_encode(session('book_date')) }}</p>   --}}


                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <section class="checkout-area pb-70">
                        <div class="card-body">
                              <div class="billing-details">
                                    <h3 class="title">{{ __('frontend.checkout.booking_summary') }}</h3>
                                    <hr>

    <div style="display: flex">
            <img style="height:100px; width:120px;object-fit: cover" src="{{ $room->image_url }}" alt="">
            <div style="padding-left: 10px;">
                <a href=" " style="font-size: 20px; color: #595959;font-weight: bold">{{ @$room->type->name }}</a>
                <p><b>{{ $room->price }} {{ __('frontend.common.per_night') }}</b></p>
            </div>

    </div>

                                    <br>

    <table class="table" style="width: 100%">
        @php
      $subtotal = $room->price * $nights * $book_data['number_of_rooms'];
      $discPct = $room->discount ?? 0;
      $discount = ($discPct / 100) * $subtotal;
        @endphp

            <tr>
                <td><p>{{ __('frontend.checkout.total_night') }} <br> <b> ( {{ $book_data['check_in'] }} - {{ $book_data['check_out'] }})</b></p></td>
                <td style="text-align: right"><p> {{ $nights }} {{ __('frontend.checkout.days') }}</p></td>
            </tr>
            <tr>
                <td><p>{{ __('frontend.checkout.total_room') }}</p></td>
                <td style="text-align: right"><p>{{ $book_data['number_of_rooms'] }}</p></td>
            </tr>
            <tr>
                <td><p>{{ __('frontend.checkout.subtotal') }}</p></td>
                <td style="text-align: right"><p>${{ $subtotal }}</p></td>
            </tr>
            <tr>
                <td><p>{{ __('frontend.checkout.discount') }}</p></td>
                <td style="text-align:right"> <p>${{ $discount }}</p></td>
            </tr>
            <tr>
                <td><p>{{ __('frontend.checkout.total') }}</p></td>
                <td style="text-align:right"> <p>${{ $subtotal-$discount }}</p></td>
            </tr>
    </table>

                              </div>
                        </div>
                  </section>

                </div>


                <div class="col-lg-8 col-md-8">
                    <div class="payment-box">
                        <div class="payment-method">

            <p>
   <input type="radio" id="cash-on-delivery" name="payment_method" value="COD" checked>
                <label for="cash-on-delivery">{{ __('frontend.checkout.payment_cod') }}</label>
            </p>

              <p>
                <input type="radio" class="pay_method" id="razorpay" name="payment_method" value="Razorpay">
                 <label for="razorpay">
                     <img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay" style="height: 20px; vertical-align: middle;">
                     {{ __('frontend.checkout.payment_razorpay_label') }}
                 </label>
              </p>

              <div id="razorpay_info" class="d-none">
                  <br>
                  <div class="alert alert-info">
                      <i class='bx bx-info-circle'></i>
                      <strong>{{ __('frontend.checkout.secure_payment_title') }}</strong> {{ __('frontend.checkout.secure_payment_body') }}
                      <br><small>{{ __('frontend.checkout.secure_payment_small') }}</small>
                  </div>
              </div>


                        </div>
       <button type="submit" class="order-btn" id="myButton" >{{ __('frontend.checkout.place_order') }}</button>

                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- Checkout Area End -->

<script type="text/javascript">
$(document).ready(function () {
    $(".pay_method, #cash-on-delivery").on('change click', function () {
        var payment_method = $('input[name="payment_method"]:checked').val();
        if (payment_method === 'Razorpay') {
            $("#razorpay_info").removeClass('d-none');
        } else {
            $("#razorpay_info").addClass('d-none');
        }
    });
});

$(function() {
    $('#checkout-form').on('submit', function(e) {
        var pay_method = $('input[name="payment_method"]:checked').val();
        if (pay_method === undefined) {
            alert(@json(__('frontend.checkout.alert_select_payment')));
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>

@endsection
