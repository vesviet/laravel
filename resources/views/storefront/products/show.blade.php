@extends('layouts.storefront')

@section('title', $product->seo_title ?? $product->name)

@section('meta')
    <meta name="description" content="{{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}">
    <meta property="og:title" content="{{ $product->seo_title ?? $product->name }}">
    <meta property="og:description" content="{{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($product->image_path)
    <meta property="og:image" content="{{ asset('storage/' . $product->image_path) }}">
    @endif
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "{{ $product->name }}",
      @if($product->image_path)
      "image": [
        "{{ asset('storage/' . $product->image_path) }}"
      ],
      @endif
      "description": "{{ $product->seo_description ?? strip_tags($product->description) }}",
      "sku": "{{ $product->sku }}",
      "offers": {
        "@type": "Offer",
        "url": "{{ request()->url() }}",
        "priceCurrency": "USD",
        "price": "{{ $product->price }}",
        "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}"
      }
    }
    </script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6 md:p-12">
        <div class="flex flex-col md:flex-row gap-12">
            <!-- Product Image -->
            <div class="w-full md:w-1/2">
                @if($product->image_path)
                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full rounded-lg object-cover">
                @else
                    <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">No Image Available</div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="w-full md:w-1/2">
                <nav class="text-sm mb-4" aria-label="Breadcrumb">
                    <ol class="list-none p-0 inline-flex">
                        <li class="flex items-center">
                            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1">Products</a>
                            <span class="mx-2 text-gray-400">/</span>
                        </li>
                        @if($product->category)
                        <li class="flex items-center">
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1">{{ $product->category->name }}</a>
                            <span class="mx-2 text-gray-400">/</span>
                        </li>
                        @endif
                        <li class="text-gray-900" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>

                <div class="flex justify-between items-start mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <livewire:wishlist-button :product="$product" />
                </div>
                <p class="text-2xl font-semibold text-blue-600 mb-6">${{ number_format($product->price, 2) }}</p>

                <div class="prose prose-sm text-gray-600 mb-8">
                    {!! nl2br(e($product->description)) !!}
                </div>

                <hr class="border-gray-200 mb-8">

                <!-- Add to Cart Livewire Component -->
                <livewire:add-to-cart-button :product="$product" />
            </div>
        </div>
    </div>
</div>
    
    <!-- Product Reviews -->
    <livewire:product-reviews :product="$product" />
</div>
@endsection
