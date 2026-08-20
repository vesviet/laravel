@extends('layouts.storefront')

@php
    $gallery = $product->gallery_images;
    if (empty($gallery) && $product->primary_image_url) {
        $gallery = [$product->primary_image_url];
    }
    $primaryImageUrl = $gallery[0] ?? null;
    $albumImages = $product->album_images;

    // Bullet points for short description
    $bullets = [];
    if (!empty($product->attributes_json['material'])) {
        $bullets[] = 'Chất liệu: ' . (is_array($product->attributes_json['material']) ? implode(', ', $product->attributes_json['material']) : $product->attributes_json['material']) . '.';
    }
    if (!empty($product->attributes_json['dimensions'])) {
        $bullets[] = 'Kích thước tiêu chuẩn: ' . $product->attributes_json['dimensions'] . '.';
    }
    if (!empty($product->attributes_json['origin'])) {
        $bullets[] = 'Xuất xứ & Thiết kế: ' . $product->attributes_json['origin'] . '.';
    }
    $bullets[] = 'Bảo hành chính hãng 24 tháng kết cấu, hỗ trợ đổi trả miễn phí trong 30 ngày.';
@endphp

@pushonce('page_title'){{ $product->seo_title ?? $product->name }} — @endpushonce
@pushonce('meta_description'){{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}@endpushonce
@pushonce('og_tags')
<meta property="og:title" content="{{ $product->seo_title ?? $product->name }}">
<meta property="og:description" content="{{ $product->seo_description ?? Str::limit(strip_tags($product->description), 160) }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ request()->url() }}">
@if($primaryImageUrl)
<meta property="og:image" content="{{ $primaryImageUrl }}">
@endif
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->name }}",
  @if($primaryImageUrl)
  "image": {!! json_encode($gallery) !!},
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

<div class="py-6 md:py-10 bg-[#F9FAFB]"
     x-data="{
        gallery: {{ json_encode($gallery) }},
        album: {{ json_encode($albumImages) }},
        currentIndex: 0,
        lightboxOpen: false,
        lightboxImage: '',
        lightboxTitle: '',
        lightboxCaption: '',
        lightboxIndex: 0,
        lightboxMode: 'gallery',

        selectImage(index) {
            this.currentIndex = index;
        },

        nextImage() {
            if (this.gallery.length > 1) {
                this.currentIndex = (this.currentIndex + 1) % this.gallery.length;
            }
        },

        prevImage() {
            if (this.gallery.length > 1) {
                this.currentIndex = (this.currentIndex - 1 + this.gallery.length) % this.gallery.length;
            }
        },

        openLightbox(index, mode = 'gallery') {
            this.lightboxMode = mode;
            this.lightboxIndex = index;
            if (mode === 'gallery') {
                this.lightboxImage = this.gallery[index];
                this.lightboxTitle = '{{ addslashes($product->name) }}';
                this.lightboxCaption = 'Ảnh chi tiết ' + (index + 1) + ' / ' + this.gallery.length;
            } else {
                const item = this.album[index];
                this.lightboxImage = item.url;
                this.lightboxTitle = item.title || '{{ addslashes($product->name) }}';
                this.lightboxCaption = (item.tag ? '[' + item.tag + '] ' : '') + (item.caption || '');
            }
            this.lightboxOpen = true;
        },

        nextLightbox() {
            if (this.lightboxMode === 'gallery') {
                this.lightboxIndex = (this.lightboxIndex + 1) % this.gallery.length;
                this.openLightbox(this.lightboxIndex, 'gallery');
            } else {
                this.lightboxIndex = (this.lightboxIndex + 1) % this.album.length;
                this.openLightbox(this.lightboxIndex, 'album');
            }
        },

        prevLightbox() {
            if (this.lightboxMode === 'gallery') {
                this.lightboxIndex = (this.lightboxIndex - 1 + this.gallery.length) % this.gallery.length;
                this.openLightbox(this.lightboxIndex, 'gallery');
            } else {
                this.lightboxIndex = (this.lightboxIndex - 1 + this.album.length) % this.album.length;
                this.openLightbox(this.lightboxIndex, 'album');
            }
        },

        scrollToDetails() {
            const el = document.getElementById('product-details-section');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }
     }"
     @keydown.window.escape="lightboxOpen = false"
     @keydown.window.arrow-right="if (lightboxOpen) nextLightbox()"
     @keydown.window.arrow-left="if (lightboxOpen) prevLightbox()">

    <div class="section-wrapper max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-xs text-[#6B7280]" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#111827] transition-colors">Trang Chủ</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-[#111827] transition-colors">Sản Phẩm</a>
            @if($product->category)
                <span aria-hidden="true">/</span>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                   class="hover:text-[#111827] transition-colors">
                    {{ $product->category->name }}
                </a>
            @endif
            <span aria-hidden="true">/</span>
            <span class="text-[#111827] font-medium truncate max-w-[240px]">{{ $product->name }}</span>
        </nav>

        {{-- ── 2-COLUMN MAIN PRODUCT BOX: Exactly matching user layout reference ── --}}
        <div class="bg-white p-6 sm:p-8 lg:p-10 border border-[#E5E7EB] shadow-sm mb-10">
            <div class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start">

                {{-- ── LEFT: Product Gallery (50% on MD, 45% on LG) ── --}}
                <div class="w-full md:w-1/2 lg:w-5/12 shrink-0 flex flex-col gap-4">

                    {{-- Main Big Product Image Viewport (Fixed constrained height, centered object-contain) --}}
                    <div class="relative w-full h-[360px] sm:h-[420px] bg-white border border-[#E5E7EB] overflow-hidden group select-none cursor-pointer flex items-center justify-center p-4"
                         @click="openLightbox(currentIndex, 'gallery')">

                        <template x-if="gallery.length > 0">
                            <div class="w-full h-full relative flex items-center justify-center">
                                <template x-for="(img, idx) in gallery" :key="'main-'+idx">
                                    <img
                                        :src="img"
                                        :alt="'{{ addslashes($product->name) }} — Hình ' + (idx + 1)"
                                        x-show="currentIndex === idx"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-98"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="max-w-full max-h-full object-contain mx-auto select-none"
                                    >
                                </template>
                            </div>
                        </template>

                        <template x-if="gallery.length === 0">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#9CA3AF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        </template>

                        {{-- HOT Badge --}}
                        @if($product->is_featured)
                            <span class="badge-hot absolute top-3 left-3 z-10 pointer-events-none" aria-label="Sản phẩm nổi bật">HOT</span>
                        @endif

                        {{-- Lightbox Expand Icon --}}
                        <button type="button"
                                @click.stop="openLightbox(currentIndex, 'gallery')"
                                class="absolute top-3 right-3 p-2 rounded-full bg-white/90 hover:bg-white text-[#374151] shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Phóng to ảnh">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Horizontal Thumbnails Row with Active Red Border (matching reference image) --}}
                    <div x-show="gallery.length > 1" class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
                        <template x-for="(img, idx) in gallery" :key="'thumb-'+idx">
                            <button
                                type="button"
                                @click="selectImage(idx)"
                                class="relative w-16 h-16 sm:w-18 sm:h-18 bg-white border p-1 shrink-0 flex items-center justify-center transition-all duration-150 focus:outline-none"
                                :class="currentIndex === idx ? 'border-[#E84444] ring-1 ring-[#E84444]' : 'border-[#E5E7EB] hover:border-[#9CA3AF] opacity-80 hover:opacity-100'"
                                :aria-label="'Xem hình ' + (idx + 1)"
                            >
                                <img :src="img" :alt="'Thumbnail ' + (idx + 1)" class="w-full h-full object-contain">
                            </button>
                        </template>
                    </div>

                </div>

                {{-- ── RIGHT: Product Details & Purchase Options (50% on MD, 55% on LG) ── --}}
                <div class="w-full md:w-1/2 lg:w-7/12 flex flex-col">

                    {{-- Title Row with Top-Right Share Icon --}}
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-[#111827] leading-snug">
                            {{ $product->name }}
                        </h1>
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết sản phẩm!');"
                            class="p-2 border border-[#E5E7EB] hover:border-[#111827] text-[#6B7280] hover:text-[#111827] transition-colors shrink-0"
                            title="Chia sẻ sản phẩm"
                            aria-label="Chia sẻ sản phẩm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Rating & Statistics Line --}}
                    @php
                        $approvedReviews = $product->reviews()->where('status', 'approved')->get();
                        $reviewsCount = $approvedReviews->count();
                        $avgRating = $reviewsCount > 0 ? round($approvedReviews->avg('rating'), 1) : 5.0;
                    @endphp
                    <div class="flex items-center gap-3 text-xs text-[#6B7280] mb-5 pb-4 border-b border-[#F3F4F6]">
                        <div class="flex items-center gap-1 text-[#F59E0B]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold text-[#111827]">{{ number_format($avgRating, 1) }}</span>
                        </div>
                        <span>|</span>
                        <a href="#product-details-section" @click.prevent="scrollToDetails()" class="hover:underline hover:text-[#111827]">
                            {{ $reviewsCount }} Đánh giá
                        </a>
                        <span>|</span>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#9CA3AF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>297 Lượt xem</span>
                        </div>
                    </div>

                    {{-- Large Price Block --}}
                    <div class="mb-6">
                        <div class="text-3xl sm:text-4xl font-bold text-[#111827] tracking-tight">
                            {{ number_format($product->price, 0, ',', '.') }}₫
                        </div>
                        <p class="text-xs text-[#6B7280] mt-1.5">
                            Giá sản phẩm đã gồm VAT và chưa gồm phí vận chuyển (nếu có)
                        </p>
                    </div>

                    {{-- Mô tả ngắn (Short Description with bullets and "Xem thêm") --}}
                    <div class="mb-6 pb-6 border-b border-[#F3F4F6]">
                        <div class="flex items-center justify-between mb-2.5">
                            <h2 class="text-xs font-bold text-[#111827] uppercase tracking-wide">Mô tả ngắn</h2>
                            <button type="button"
                                    @click="scrollToDetails()"
                                    class="text-xs font-medium text-[#2563EB] hover:underline">
                                Xem thêm
                            </button>
                        </div>
                        <ul class="space-y-1.5 text-xs text-[#4B5563] leading-relaxed">
                            @if($product->description)
                                <li class="flex items-start gap-2">
                                    <span class="text-[#9CA3AF] select-none">•</span>
                                    <span>{{ Str::limit(strip_tags($product->description), 160) }}</span>
                                </li>
                            @endif
                            @foreach($bullets as $bullet)
                                <li class="flex items-start gap-2">
                                    <span class="text-[#9CA3AF] select-none">•</span>
                                    <span>{{ $bullet }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Livewire Quantity + Actions Box --}}
                    <livewire:add-to-cart-button :product="$product" />

                </div>

            </div>
        </div>

        {{-- ── FULL-WIDTH BOTTOM TAB BAR: "Chi Tiết Sản Phẩm" ── --}}
        <div id="product-details-section" class="bg-white border border-[#E5E7EB] shadow-sm mb-12" x-data="{ tab: 'details' }">

            {{-- Gray Header Bar with Full-Width Tab Header --}}
            <div class="bg-[#F3F4F6] border-b border-[#E5E7EB] px-6 flex items-center gap-8" role="tablist">
                <button
                    type="button"
                    @click="tab = 'details'"
                    :class="tab === 'details' ? 'border-b-2 border-[#111827] text-[#111827] font-bold bg-white' : 'text-[#6B7280] font-medium hover:text-[#111827]'"
                    class="py-4 px-3 text-sm tracking-wide transition-colors focus:outline-none -mb-px"
                    role="tab"
                    :aria-selected="tab === 'details'">
                    Chi Tiết Sản Phẩm
                </button>
                @if($product->attributes_json)
                    <button
                        type="button"
                        @click="tab = 'specs'"
                        :class="tab === 'specs' ? 'border-b-2 border-[#111827] text-[#111827] font-bold bg-white' : 'text-[#6B7280] font-medium hover:text-[#111827]'"
                        class="py-4 px-3 text-sm tracking-wide transition-colors focus:outline-none -mb-px"
                        role="tab"
                        :aria-selected="tab === 'specs'">
                        Thông Số Kỹ Thuật
                    </button>
                @endif
                <button
                    type="button"
                    @click="tab = 'reviews'"
                    :class="tab === 'reviews' ? 'border-b-2 border-[#111827] text-[#111827] font-bold bg-white' : 'text-[#6B7280] font-medium hover:text-[#111827]'"
                    class="py-4 px-3 text-sm tracking-wide transition-colors focus:outline-none -mb-px"
                    role="tab"
                    :aria-selected="tab === 'reviews'">
                    Đánh Giá Khách Hàng ({{ $product->reviews()->where('status', 'approved')->count() }})
                </button>
            </div>

            {{-- Tab Content Panel --}}
            <div class="p-6 md:p-8 lg:p-10">
                {{-- Chi Tiết Tab --}}
                <div x-show="tab === 'details'" class="max-w-4xl text-sm text-[#374151] leading-relaxed space-y-4">
                    @if($product->description)
                        <div class="font-normal space-y-3">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <p class="text-[#9CA3AF]">Chưa có thông tin chi tiết.</p>
                    @endif
                </div>

                {{-- Specs Tab --}}
                @if($product->attributes_json)
                    <div x-show="tab === 'specs'" class="max-w-2xl" style="display: none;">
                        <table class="w-full text-xs text-left border-collapse border border-[#E5E7EB]">
                            <tbody>
                                @foreach($product->attributes_json as $key => $value)
                                    @continue(in_array($key, ['secondary_image', 'gallery', 'album']))
                                    <tr class="border-b border-[#E5E7EB] odd:bg-white even:bg-[#F9FAFB]">
                                        <th class="py-3 px-4 font-semibold text-[#374151] w-1/3 uppercase text-[10px] tracking-wider border-r border-[#E5E7EB]">
                                             {{ str_replace('_', ' ', $key) }}
                                        </th>
                                        <td class="py-3 px-4 text-[#4B5563]">
                                            {{ is_array($value) ? implode(', ', $value) : $value }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Reviews Tab --}}
                <div x-show="tab === 'reviews'" class="max-w-4xl" style="display: none;">
                    <livewire:product-reviews :product="$product" />
                </div>
            </div>

        </div>

        {{-- ── LIFESTYLE ALBUM LOOKBOOK: In The Living Space ── --}}
        @if(!empty($albumImages))
            <section class="mb-16" aria-label="Album Không Gian Sống">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-4 border-b border-[#E5E7EB]">
                    <div>
                        <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#E84444] mb-1">
                            LIFESTYLE LOOKBOOK
                        </p>
                        <h2 class="text-xl sm:text-2xl font-bold text-[#111827]">
                            Album Không Gian Sống — {{ $product->name }}
                        </h2>
                    </div>
                    <p class="text-xs text-[#6B7280] max-w-md mt-2 md:mt-0">
                        Hình ảnh phối cảnh thực tế trong các không gian kiến trúc nội thất hiện đại.
                    </p>
                </div>

                {{-- Mosaic Lookbook Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                    @foreach($albumImages as $idx => $item)
                        @php
                            $isLarge = ($idx === 0 || $idx === 3);
                            $colSpan = $isLarge ? 'lg:col-span-7' : 'lg:col-span-5';
                            $aspectClass = $isLarge ? 'aspect-[16/10]' : 'aspect-square';
                        @endphp
                        <div class="{{ $colSpan }} group relative bg-white border border-[#E5E7EB] overflow-hidden cursor-pointer shadow-sm"
                             @click="openLightbox({{ $idx }}, 'album')">

                            <div class="w-full {{ $aspectClass }} overflow-hidden bg-[#F3F4F6]">
                                <img
                                    src="{{ $item['url'] }}"
                                    alt="{{ $item['title'] ?? $product->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                >
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-85 group-hover:opacity-95 transition-opacity flex flex-col justify-end p-5 sm:p-6 text-white">
                                @if(!empty($item['tag']))
                                    <span class="text-[9px] tracking-[0.2em] uppercase font-bold text-[#FCA5A5] mb-1">
                                        {{ $item['tag'] }}
                                    </span>
                                @endif
                                <h3 class="text-sm sm:text-base font-semibold text-white mb-1">
                                    {{ $item['title'] ?? $product->name }}
                                </h3>
                                @if(!empty($item['caption']))
                                    <p class="text-xs text-white/80 font-light line-clamp-2 mb-2">
                                        {{ $item['caption'] }}
                                    </p>
                                @endif
                                <span class="text-[10px] tracking-wider uppercase font-semibold text-white/90 group-hover:text-white flex items-center gap-1.5">
                                    <span>Xem ảnh phóng to</span>
                                    <span>&rarr;</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── RELATED PRODUCTS SECTION ── --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <section class="mt-14 pt-12 border-t border-[#E5E5E5]" aria-label="Sản phẩm tương tự">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-[10px] uppercase font-semibold tracking-[0.2em] text-[#888888] block mb-1">Bộ Sưu Tập</span>
                        <h2 class="text-xl sm:text-2xl font-light text-[#23232C] uppercase tracking-wide">
                            Sản Phẩm Tương Tự
                        </h2>
                    </div>
                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-xs uppercase font-medium tracking-wider text-[#23232C] hover:text-[#E84444] link-underline">
                            Xem tất cả &rarr;
                        </a>
                    @endif
                </div>

                <div class="product-grid">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>

    {{-- ── FULLSCREEN LIGHTBOX MODAL ── --}}
    <div x-show="lightboxOpen"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex flex-col justify-between bg-black/95 p-4 sm:p-6 select-none"
         style="display: none;">

        {{-- Top Bar --}}
        <div class="flex items-center justify-between text-white border-b border-white/10 pb-3">
            <div>
                <p class="text-[10px] tracking-[0.2em] uppercase text-white/60" x-text="lightboxMode === 'album' ? 'LIFESTYLE LOOKBOOK' : 'HÌNH ẢNH SẢN PHẨM'"></p>
                <h3 class="text-sm font-semibold text-white" x-text="lightboxTitle"></h3>
            </div>
            <button type="button"
                    @click="lightboxOpen = false"
                    class="text-white/80 hover:text-white text-3xl font-light focus:outline-none cursor-pointer"
                    aria-label="Đóng phóng to ảnh">
                &times;
            </button>
        </div>

        {{-- Center Image --}}
        <div class="relative flex-1 flex items-center justify-center py-4 overflow-hidden">
            <button type="button"
                    @click.stop="prevLightbox()"
                    class="absolute left-2 sm:left-4 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/30 text-white flex items-center justify-center transition-all"
                    aria-label="Ảnh trước">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <img :src="lightboxImage"
                 :alt="lightboxTitle"
                 class="max-w-full max-h-[70vh] object-contain shadow-2xl">

            <button type="button"
                    @click.stop="nextLightbox()"
                    class="absolute right-2 sm:right-4 z-20 w-10 h-10 rounded-full bg-white/10 hover:bg-white/30 text-white flex items-center justify-center transition-all"
                    aria-label="Ảnh sau">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-white/10 pt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white text-xs">
            <p class="text-white/80 font-light max-w-lg truncate" x-text="lightboxCaption"></p>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-sm scrollbar-none">
                <template x-if="lightboxMode === 'gallery'">
                    <template x-for="(img, idx) in gallery" :key="'lb-g-'+idx">
                        <button type="button"
                                @click="openLightbox(idx, 'gallery')"
                                class="w-10 h-10 flex-shrink-0 bg-white/10 overflow-hidden"
                                :class="lightboxIndex === idx ? 'ring-2 ring-[#E84444] opacity-100' : 'opacity-40 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </template>
                <template x-if="lightboxMode === 'album'">
                    <template x-for="(item, idx) in album" :key="'lb-a-'+idx">
                        <button type="button"
                                @click="openLightbox(idx, 'album')"
                                class="w-10 h-10 flex-shrink-0 bg-white/10 overflow-hidden"
                                :class="lightboxIndex === idx ? 'ring-2 ring-[#E84444] opacity-100' : 'opacity-40 hover:opacity-100'">
                            <img :src="item.url" class="w-full h-full object-cover">
                        </button>
                    </template>
                </template>
            </div>
        </div>
    </div>

</div>

@endsection
