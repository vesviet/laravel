@extends('layouts.storefront')

@pushonce('page_title')Trang Chủ @endpushonce
@pushonce('meta_description')MYSHOP — Cửa hàng thời trang hiện đại. Sản phẩm chất lượng cao, thiết kế tinh tế. Giao hàng toàn quốc miễn phí.@endpushonce
@pushonce('og_tags')
<meta property="og:title" content="Trang Chủ — MYSHOP">
<meta property="og:description" content="Cửa hàng thời trang hiện đại. Sản phẩm chất lượng cao, thiết kế tinh tế.">
<meta property="og:type" content="website">
@endpushonce

@section('content')
    @include('storefront.home.partials.hero')
    @include('storefront.home.partials.category-banners')
    @include('storefront.home.partials.featured-products', ['products' => $featuredProducts])
    @include('storefront.home.partials.collection-banner')
    @include('storefront.home.partials.new-arrivals', ['products' => $newArrivals])
    @include('storefront.home.partials.trust-badges')
@endsection
