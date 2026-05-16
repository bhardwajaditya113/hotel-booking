@extends('frontend.main_master')
@section('main')

  <!-- Inner Banner -->
  <div class="inner-banner inner-bg3">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li>
                    <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                </li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>{{ __('frontend.gallery.page_title') }}</li>
            </ul>
            <h3>{{ __('frontend.gallery.page_title') }}</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Gallery Area -->
<div class="gallery-area pt-100 pb-70">
    <div class="container">
        <div class="tab gallery-tab">


            <div class="tab_content current active pt-45">
                <div class="tabs_item current">
                    <div class="gallery-tab-item">
                        <div class="gallery-view">
                            <div class="row">

                @foreach ($gallery as $item)
                <div class="col-lg-4 col-sm-6">
                    <div class="single-gallery">
                        <img src="{{ \App\Support\MediaUrl::resolve($item->photo_name, 'upload/gallery') }}" alt="">
                        <a href="{{ \App\Support\MediaUrl::resolve($item->photo_name, 'upload/gallery') }}" class="gallery-icon">
                            <i class='bx bx-plus'></i>
                        </a>
                    </div>
                </div>
                @endforeach


                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>
<!-- Gallery Area End -->


@endsection
