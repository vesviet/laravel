@extends('layouts.storefront')

@pushonce('page_title')Trang Chủ — @endpushonce
@pushonce('meta_description')MYSHOP — Cửa hàng nội thất và phong cách sống hiện đại phong cách Bắc Âu Scandinavian. Sản phẩm chất lượng cao, thiết kế tối giản, giao hàng toàn quốc.@endpushonce
@pushonce('og_tags')
<meta property="og:title" content="Trang Chủ — MYSHOP">
<meta property="og:description" content="Cửa hàng nội thất phong cách Bắc Âu tối giản. Sản phẩm chất lượng cao, thiết kế tinh tế.">
<meta property="og:type" content="website">
@endpushonce

@section('content')
    {{-- Section 1: Hero Slider --}}
    @include('storefront.home.partials.hero', ['heroSlides' => $heroSlides])

    {{-- Section 2: 2-Column Promo Banners --}}
    @include('storefront.home.partials.category-banners', ['promoBanners' => $promoBanners])

    {{-- Section 3: Intro Heading & Description --}}
    @include('storefront.home.partials.intro')

    {{-- Section 4: Featured Products (4 Columns) --}}
    @include('storefront.home.partials.featured-products', ['products' => $featuredProducts])

    {{-- Section 5: New Arrivals (4 Columns) --}}
    @include('storefront.home.partials.new-arrivals', ['products' => $newArrivals])

    {{-- Section 6: Featured Collections (3 Columns) --}}
    @include('storefront.home.partials.collection-banner', ['collectionBanners' => $collectionBanners])

    {{-- Section 7: Trust Badges (3 Columns) --}}
    @include('storefront.home.partials.trust-badges')
@endsection
