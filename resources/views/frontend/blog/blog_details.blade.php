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
                <li>{{ __('frontend.blog.details_breadcrumb') }}</li>
            </ul>
            <h3>{{ $blog->post_titile }}</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

<!-- Blog Details Area -->
<div class="blog-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="blog-article">
                    <div class="blog-article-img">
                        <img src="{{ \App\Support\MediaUrl::resolve($blog->post_image, 'upload/blog') }}" alt="" style="max-width: 100%; height: auto;">
                    </div>

                    <div class="blog-article-title">
                        <h2>{{ $blog->post_titile }}</h2>
                        <ul>
                            
                            <li>
                                <i class='bx bx-user'></i>
                                {{ $blog->user->name ?? __('frontend.common.guest') }}
                            </li>

                            <li>
                                <i class='bx bx-calendar'></i>
                                {{ $blog->created_at->format('M d Y')  }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="article-content">
                        <p>
                            {!! $blog->long_descp !!}
                        </p>
                    </div>
@php
    // Comments are provided by the controller in $comments
    $comments = $comments ?? collect();
@endphp
                    <div class="comments-wrap">
                        <h3 class="title">{{ __('frontend.blog.comments_title') }}</h3>
                        <ul>
                            @foreach ($comments as $com) 
                            <li>
                                <img src="{{ (!empty($com->user?->photo)) ? url('upload/user_images/'.$com->user->photo) : url('upload/no_image.jpg') }}" alt="Image" style="width: 50px; height:50px;">
                                <h3>{{ $com->user->name ?? __('frontend.common.guest') }}</h3>
                                <span>{{ $com->created_at->format('M d Y') }}</span>
                                <p>
                                    {{ $com->message }}
                                </p>
                                 
                            </li>
                            @endforeach
                            
                        </ul>
                    </div>

                    <div class="comments-form">
                        <div class="contact-form">
      
                            <h2>{{ __('frontend.blog.leave_comment') }}</h2>
    @php
        $userData = Auth::user();
    @endphp

    @auth            
    <form method="POST" action="{{ route('store.comment') }}" >
        @csrf

        <div class="row">
             
            <input type="hidden" name="post_id" value="{{ $blog->id }}">

            @if ($userData)
                <input type="hidden" name="user_id" value="{{ $userData->id }}">
            @endif


            <div class="col-lg-12 col-md-12">
                <div class="form-group">
                    <textarea name="message" class="form-control" id="message" cols="30" rows="8" required data-error="{{ __('frontend.blog.data_error_message') }}" placeholder="{{ __('frontend.blog.placeholder_message') }}"></textarea>
                </div>
            </div>

            
            <div class="col-lg-12 col-md-12">
                <button type="submit" class="default-btn btn-bg-three">
                    {{ __('frontend.blog.post_comment') }}
                </button>
            </div>
        </div>
    </form>

    @else

    <p>{{ __('frontend.blog.login_prefix') }} <a href="{{ route('login') }}">{{ __('frontend.blog.login_link') }}</a> {{ __('frontend.blog.login_suffix') }}</p>

    @endauth   
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="side-bar-wrap">
                    <div class="search-widget">
                        <form class="search-form">
                            <input type="search" class="form-control" placeholder="{{ __('frontend.common.search_placeholder') }}">
                            <button type="submit">
                                <i class="bx bx-search"></i>
                            </button>
                        </form>
                    </div>

                    <div class="services-bar-widget">
                        <h3 class="title">{{ __('frontend.blog.category_title') }}</h3>
                        <div class="side-bar-categories">
                            @foreach ($bcategory as $cat) 
                            <ul>
                                <li>
                                    <a href="{{ route('blog.cat.list', $cat->id) }}">{{ $cat->category_name }}</a>
                                </li> 
                            </ul>
                            @endforeach
                        </div>
                    </div>

                    <div class="side-bar-widget">
                        <h3 class="title">{{ __('frontend.blog.recent_posts') }}</h3>
                        <div class="widget-popular-post">
                            
                            @foreach ($lpost as $post)   
                            <article class="item">
                                <a href="{{ route('blog.details', $post->post_slug) }}" class="thumb">
                <img src="{{ \App\Support\MediaUrl::resolve($post->post_image, 'upload/blog') }}" alt="" style="width: 80px; height:80px;">      
                                </a>
                                <div class="info">
                                    <h4 class="title-text">
                                        <a href="{{ route('blog.details', $post->post_slug) }}">
                                            {{ $post->post_titile }}
                                        </a>
                                    </h4>
                                    <ul>
                                        <li>
                                            <i class='bx bx-user'></i>
                                            29K
                                        </li>
                                        <li>
                                            <i class='bx bx-message-square-detail'></i>
                                            15K
                                        </li>
                                    </ul>
                                </div>
                            </article>
                            @endforeach

                          
                        </div>
                    </div>

                  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog Details Area End -->

@endsection