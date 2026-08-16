@extends('layouts.storefront')

@pushonce('page_title'){{ $product->seo_title ?? $product->name }} @endpushonce
@pushonce('meta_description'){{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}@endpushonce
@pushonce('og_tags')
<meta property="og:title" content="{{ $product->seo_title ?? $product->name }}">
<meta property="og:description" content="{{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ request()->url() }}">
@if($product->image_path)
<meta property="og:image" content="{{ Storage::url($product->image_path) }}">
@endif
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->name }}",
  @if($product->image_path)
  "image": ["{{ Storage::url($product->image_path) }}"],
  @endif
  "description": "{{ $product->seo_description ?? strip_tags($product->description) }}",
  "sku": "{{ $product->sku }}",
  "offers": {
    "@@type": "Offer",
    "url": "{{ request()->url() }}",
    "priceCurrency": "VND",
    "price": "{{ $product->price }}",
    "availability": "https://schema.org/{{ $product->stock > 0 ? 'InStock' : 'OutOfStock' }}"
  }
}
</script>
@endpushonce

@section('content')

<div class="py-8 md:py-14">
    <div class="section-wrapper">

        {{-- Breadcrumb --}}
        <nav class="mb-8 flex items-center gap-2 text-[10px] tracking-[0.15em] uppercase text-[#888888]" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#1a1a1a] transition-colors">Trang Chủ</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-[#1a1a1a] transition-colors">Sản Phẩm</a>
            @if($product->category)
                <span aria-hidden="true">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                   class="hover:text-[#1a1a1a] transition-colors">
                    {{ $product->category->name }}
                </a>
            @endif
            <span aria-hidden="true">/</span>
            <span class="text-[#1a1a1a]">{{ Str::limit($product->name, 30) }}</span>
        </nav>

        {{-- ── 2-column layout: Gallery (left) + Info (right) ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16">

            {{-- ── LEFT: Product image gallery ── --}}
            <div x-data="{ selectedImage: '{{ $product->image_path ? Storage::url($product->image_path) : '' }}' }">

                {{-- Main image --}}
                <div class="aspect-square bg-[#E8E4DF] overflow-hidden">
                    @if($product->image_path)
                        <img
                            x-bind:src="selectedImage"
                            src="{{ Storage::url($product->image_path) }}"
                            alt="{{ $product->name }}"
                            fetchpriority="high"
                            width="700"
                            height="700"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#b0a89e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Thumbnail strip (future: multiple images via variants) --}}
                @if($product->image_path)
                    <div class="flex gap-2 mt-3">
                        <button
                            @click="selectedImage = '{{ Storage::url($product->image_path) }}'"
                            class="w-16 h-16 flex-shrink-0 overflow-hidden border-2 border-[#1a1a1a] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                            aria-label="Ảnh 1">
                            <img src="{{ Storage::url($product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </button>
                    </div>
                @endif
            </div>

            {{-- ── RIGHT: Product info ── --}}
            <div class="flex flex-col">

                {{-- Category label --}}
                @if($product->category)
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase text-[#888888] mb-3">
                        {{ $product->category->name }}
                    </p>
                @endif

                {{-- Product name --}}
                <h1 class="text-2xl md:text-3xl font-medium tracking-wide leading-snug mb-4">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <p class="text-xl font-medium mb-6">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </p>

                {{-- Short description --}}
                @if($product->description)
                    <div class="text-sm text-[#555] leading-relaxed mb-8 font-light border-t border-[#E5E5E5] pt-6">
                        {!! nl2br(e(Str::limit(strip_tags($product->description), 300))) !!}
                    </div>
                @endif

                {{-- Stock status --}}
                <p class="text-xs tracking-widest uppercase mb-6 {{ $product->stock > 0 ? 'text-green-700' : 'text-[#E84444]' }}" aria-live="polite">
                    {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                </p>

                {{-- Variant picker (if variants exist) --}}
                @if($product->variants->isNotEmpty())
                    <div class="mb-6">
                        <p class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-3">Lựa Chọn</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <button class="border border-[#E5E5E5] text-xs px-4 py-2 hover:border-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]">
                                    {{ $variant->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add to cart --}}
                @if($product->stock > 0)
                    <livewire:add-to-cart-button :product="$product" />
                @else
                    <button disabled
                            class="btn-dark opacity-40 cursor-not-allowed w-full mb-4">
                        Hết Hàng
                    </button>
                @endif

                {{-- Wishlist --}}
                @auth('customer')
                    <livewire:wishlist-button :product="$product" :key="'detail-wb-'.$product->id" />
                @endauth

                {{-- Trust signals --}}
                <div class="mt-8 pt-6 border-t border-[#E5E5E5] flex flex-col gap-3">
                    <div class="flex items-center gap-3 text-xs text-[#888888]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Miễn phí giao hàng cho đơn từ 500.000₫
                    </div>
                    <div class="flex items-center gap-3 text-xs text-[#888888]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Đổi trả trong 30 ngày
                    </div>
                </div>

                {{-- SKU --}}
                @if($product->sku)
                    <p class="mt-4 text-[10px] text-[#888888] tracking-widest uppercase">SKU: {{ $product->sku }}</p>
                @endif

            </div>
        </div>

        {{-- ── Product Description Tabs ── --}}
        <div class="mt-16 border-t border-[#E5E5E5]" x-data="{ tab: 'description' }">

            <div class="flex border-b border-[#E5E5E5]" role="tablist" aria-label="Thông tin sản phẩm">
                <button
                    @click="tab = 'description'"
                    :class="tab === 'description' ? 'border-b-2 border-[#1a1a1a] text-[#1a1a1a]' : 'text-[#888888]'"
                    class="text-[11px] tracking-[0.15em] uppercase py-4 pr-8 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                    role="tab"
                    :aria-selected="tab === 'description'"
                    id="tab-desc">
                    Mô Tả
                </button>
                @if($product->attributes_json)
                    <button
                        @click="tab = 'specs'"
                        :class="tab === 'specs' ? 'border-b-2 border-[#1a1a1a] text-[#1a1a1a]' : 'text-[#888888]'"
                        class="text-[11px] tracking-[0.15em] uppercase py-4 pr-8 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                        role="tab"
                        :aria-selected="tab === 'specs'"
                        id="tab-specs">
                        Thông Số
                    </button>
                @endif
            </div>

            {{-- Description tab --}}
            <div x-show="tab === 'description'" class="py-8 max-w-2xl" role="tabpanel" aria-labelledby="tab-desc">
                @if($product->description)
                    <div class="text-sm text-[#555] leading-relaxed font-light">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @else
                    <p class="text-sm text-[#888888]">Chưa có mô tả.</p>
                @endif
            </div>

            {{-- Specs tab --}}
            @if($product->attributes_json)
                <div x-show="tab === 'specs'" class="py-8" role="tabpanel" aria-labelledby="tab-specs" style="display: none;">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl">
                        @foreach($product->attributes_json as $key => $value)
                            <div class="border-b border-[#E5E5E5] pb-3">
                                <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">{{ $key }}</dt>
                                <dd class="text-sm font-light">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

        </div>

    </div>
</div>

@endsection
