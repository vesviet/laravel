@extends('layouts.app')

@section('title', 'Sản phẩm - Máy Điện Giải Sài Gòn')
@section('meta_description', 'Danh sách máy điện giải nước ion kiềm chính hãng, giá tốt nhất.')

@section('content')
<div style="height: var(--header-height);"></div>

<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Bộ sưu tập</div>
            <h1 class="section-title">Sản Phẩm Của Chúng Tôi</h1>
        </div>

        {{-- Category Filter --}}
        @if($categories->count())
        <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;margin-bottom:2.5rem;">
            <a href="{{ route('products.index') }}" 
               class="btn btn-sm @if(!request('category')) btn-primary @else btn-outline" style="color:var(--color-navy);border-color:var(--color-gray-200) @endif">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                   class="btn btn-sm @if(request('category') === $cat->slug) btn-primary @else btn-outline" style="color:var(--color-navy);border-color:var(--color-gray-200) @endif">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
        @endif

        <div class="product-grid">
            @forelse($products as $product)
                <div class="product-card">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="product-card-image">
                            @if($product->getFirstMediaUrl('main_image', 'medium'))
                                <img src="{{ $product->getFirstMediaUrl('main_image', 'medium') }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--color-gray-300);font-size:3rem;">📦</div>
                            @endif
                            @if($product->discount_percent)
                                <span class="product-card-badge">-{{ $product->discount_percent }}%</span>
                            @endif
                        </div>
                    </a>
                    <div class="product-card-body">
                        @if($product->category)
                            <div class="product-card-category">{{ $product->category->name }}</div>
                        @endif
                        <h3 class="product-card-name">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <div class="product-card-price">
                            <span class="price-current">{{ $product->formatted_price }}</span>
                            @if($product->formatted_original_price)
                                <span class="price-original">{{ $product->formatted_original_price }}</span>
                            @endif
                        </div>
                        <div style="margin-top:0.75rem;">
                            @livewire('add-to-cart-button', [
                                'productId' => $product->id,
                                'productName' => $product->name,
                                'productPrice' => $product->price,
                                'productImage' => $product->getFirstMediaUrl('main_image', 'thumb'),
                                'size' => 'sm',
                            ], key('list-atc-'.$product->id))
                        </div>
                    </div>
                </div>
            @empty
                <p style="grid-column:1/-1;text-align:center;color:var(--color-gray-500);padding:3rem;">
                    Không tìm thấy sản phẩm nào.
                </p>
            @endforelse
        </div>

        {{ $products->withQueryString()->links() }}
    </div>
</section>
@endsection
