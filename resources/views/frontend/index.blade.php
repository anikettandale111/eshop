@extends('frontend.layouts.master')
<style>
    .video-container iframe {
        position: relative;
        width: 100%;
        height: 60%;
        padding-top: 10px;
        padding-bottom: 50px;
        padding-left: 100px;
        padding-right: 100px;
    }

    @media (max-width: 767px) {
        .video-container iframe {
            height: 40%;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 20px;
            padding-right: 20px;
        }
    }

    .strikethrough {
        text-decoration: line-through;
    }

    .marquee-container {
        width: 100%;
        overflow: hidden;
    }

    .marquee {
        display: inline-block;
        /* Display content in a single line */
        white-space: nowrap;
        /* Prevent line breaks */
        animation: marquee 30s linear infinite;
        animation-delay: -15s;
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }
</style>
@section('content')
<!-- Hero Slider Starts -->
<!-- @if (count($banners) > 0)
<section class="categories-area d-md-block pt-60">
    <div class="home-slides owl-carousel owl-theme">
        @foreach ($banners as $banner)
        <a href="">
            <div class="main-banner" style="background-image:url({{ asset($banner->image) }})">
                <div class="d-table">
                    <div class="d-table-cell">
                        <div class="container">
                            <div class="main-banner-content">
                                {!! html_entity_decode($banner->content) !!}
                                <div id="heroNav" class="hero-owl-nav"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif -->

<div class="">
    <div class="video-container">
        <iframe width="560" height="315" src="{{asset('videos/OpeningVideo.mp4')}}" controls autoplay muted title="YouTube video player" frameborder="0" allowfullscreen></iframe>
    </div>
</div>

@if ($latest_products->count() > 0)
<div class="marquee-container">
    <div class="marquee" id="marquee">
        @foreach ($latest_products as $item)
        <div style="display: inline-block;
    margin-right: 20px;
    background: wheat;
    border-radius: 10%;
    text-align: center;
    padding: 10px;
">
            <h4 style="color:black">
                {{$item->title}}
                <div class="price">
                    @if ($item->discount > 0)
                    <span class="old-price strikethrough" style="color:red">{{ Helper::currency_converter($item->unit_price) }}</span>
                    <span class="new-price" style="color:green">{{ Helper::currency_converter($item->purchase_price) }}</span>
                    @else
                    <span class="equal-price" style="color:green">{{ Helper::currency_converter($item->purchase_price) }}</span>
                    @endif
                </div>
            </h4>
        </div>
        @endforeach
    </div>
</div>
@endif
<!-- Hero Slider Ends -->
@if ($categories->count() > 0)
<section class="categories-area d-md-block pt-20">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">Popular</span>
            <h2>Categories</h2>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-6 col-sm-6">
                <div class="row">
                    <div class="owl-carousel owl-theme categories-slides">
                        @foreach ($categories as $key => $cat)
                        <div class="single-category-box" data-slick-index="{{($key+1)}}" style="width: 166px;">
                            <figure class=" img-hover-scale overflow-hidden">
                                <a href="{{ route('product.category', $cat->slug) }}" tabindex="0"><img src="{{ $cat->banner ? asset($cat->banner) : Helper::DefaultImage() }}" alt="category-image"></a>
                            </figure>
                            <h5><a href="{{ route('product.category', $cat->slug) }}" tabindex="0">{{ ucfirst($cat->title) }}</a></h5>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($featured_product->count() > 0)
<section class="products-area pt-20">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">Featured</span>
            <h2>Products
                <a href="{{route('viewAllProducts','featured')}}" class="view-all">View All</a>
            </h2>
        </div>
        <div class="owl-carousel owl-theme products-slides home_products">
            @foreach ($featured_product as $item)
            @include('frontend.partials._single_product', compact('item'))
            @endforeach
        </div>
    </div>
</section>
@endif
<!-- <div class="">
        <div class="video-container">
        <iframe width="560" height="315" src="{{asset('videos/OpeningVideo.mp4')}}" controls autoplay muted title="YouTube video player" frameborder="0" allowfullscreen></iframe>
        </div>
    </div> -->
@if ($featured_category)
<!-- Occasion Dresses -->
<section class="products-area pt-20">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">Shop Now</span>
            <h2>{{ ucfirst($featured_category->title) }}
                <a href="{{ route('product.category', $featured_category->slug) }}" class="view-all">View All</a>
            </h2>
        </div>
        <div class="owl-carousel owl-theme products-slides">
            @foreach ($featured_category->products as $item)
            @include('frontend.partials._single_product', compact('item'))
            @endforeach

        </div>
    </div>
</section>
@endif


@if ($promo_banners)
<section class="products-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="banner-bg wow fadeIn animated animated animated" style="background-image: url({{ asset($promo_banners->image) }}); visibility: visible;background-position: center;
                                 background-size: cover;
                                 padding: 50px;">
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@if ($latest_products->count() > 0)
<section class="products-area pt-20">
    <div class="container">
        <div class="section-title">
            <span class="sub-title">Latest</span>
            <h2>Products
                <a href="{{route('viewAllProducts','latest')}}" class="view-all">View All</a>
            </h2>
        </div>
        <div class="owl-carousel owl-theme products-slides home_products">
            @foreach ($latest_products as $item)
            @include('frontend.partials._single_product', compact('item'))
            @endforeach
        </div>
    </div>
</section>
@endif



@endsection

@push('styles')
<style>
    .offer-content p {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        margin-bottom: 24px;
        height: 54px;
    }
</style>
@endpush