@php
    $testimonial = collect();
@endphp
<div class="testimonials-area-three pb-70">
    <div class="container">
        <div class="section-title text-center">
            <span class="sp-color">{{ __('site.testimonials.eyebrow') }}</span>
            <h2>{{ __('site.testimonials.title') }}</h2>
        </div>
        <div class="row align-items-center pt-45">
            <div class="col-lg-6 col-md-6">
                <div class="testimonials-img-two">
                    <img src="{{ asset('frontend/assets/img/testimonials/testimonials-img5.jpg') }}" alt="Images">
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="testimonials-slider-area owl-carousel owl-theme">

                    @foreach ($testimonial as $item)
                    <div class="testimonials-slider-content">
                        <i class="flaticon-left-quote"></i>
                        <p>
                            {{ $item->message }}
                        </p>
                        <ul>
                            <li>
                                <img src="{{ \App\Support\MediaUrl::resolve($item->image) }}" alt="">
                                <h3>{{ $item->name }}</h3>
                                <span>{{ $item->city }}</span>
                            </li>
                        </ul>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
