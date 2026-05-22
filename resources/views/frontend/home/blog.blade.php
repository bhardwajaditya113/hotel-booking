@php
    $blog = collect();
@endphp
<div class="blog-area pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <span class="sp-color">{{ __('site.blog_section.eyebrow') }}</span>
            <h2 class="mb-2">{{ __('site.blog_section.title') }}</h2>
            <a href="{{ route('blog.list') }}" class="read-btn d-inline-block">{{ __('site.blog_section.view_all') }}</a>
        </div>
        <div class="row pt-45">

            @foreach ($blog as $item)

            <div class="col-lg-4 col-md-6">
                <div class="blog-item">
                    <a href="{{ route('blog.details', $item->post_slug) }}">
                        <img src="{{ \App\Support\MediaUrl::resolve($item->post_image, 'upload/blog') }}" alt="">
                    </a>
                    <div class="content">
                        <ul>
                            <li>{{ $item->created_at->format('M d Y') }}</li>
                            <li><i class='bx bx-user'></i>29K</li>
                            <li><i class='bx bx-message-alt-dots'></i>15K</li>
                        </ul>
                        <h3>
                            <a href="{{ route('blog.details', $item->post_slug) }}">{{ $item->post_titile }}</a>
                        </h3>
                        <p>{{ $item->short_descp }}</p>
                        <a href="{{ route('blog.details', $item->post_slug) }}" class="read-btn">
                            {{ __('site.blog_section.read_more') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
