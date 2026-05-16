@extends('frontend.main_master')
@section('main')

    <!-- Inner Banner -->
    <div class="inner-banner inner-bg4">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="{{ url('/') }}">{{ __('site.nav.home') }}</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>{{ __('frontend.blog.page_title') }}</li>
                </ul>
                <h3>{{ __('frontend.blog.page_title') }}</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Blog Style Area -->
    <div class="blog-style-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    
                    @foreach ($blog as $item ) 
                   
                    <div class="col-lg-12">
                        <div class="blog-card">
                            <div class="row align-items-center">
                                <div class="col-lg-5 col-md-4 p-0">
                                    <div class="blog-img">
                                        <a href="{{ route('blog.details', $item->post_slug) }}">
                                            <img src="{{ \App\Support\MediaUrl::resolve($item->post_image, 'upload/blog') }}" alt="">
                                        </a>
                                    </div>
                                </div>

                <div class="col-lg-7 col-md-8 p-0">
                    <div class="blog-content">
                <span>{{ $item->created_at->format('M d Y')  }}</span>
                        <h3>
                            <a href="{{ route('blog.details', $item->post_slug) }}">{{ $item->post_titile }}</a>
                        </h3>
                        <p>{{ $item->short_descp }}</p>
                        <a href="{{ route('blog.details', $item->post_slug) }}" class="read-btn">
                            {{ __('frontend.common.read_more') }}
                        </a>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                

                    <div class="col-lg-12 col-md-12">
                        <div class="pagination-area">

                            {{ $blog->links() }}

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
    <!-- Blog Style Area End -->





@endsection