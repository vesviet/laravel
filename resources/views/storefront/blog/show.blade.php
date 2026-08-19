@extends('layouts.storefront')

@pushonce('page_title'){{ $post->seo_title ?: $post->title }} — @endpushonce
@pushonce('meta_description'){{ $post->seo_description ?: $post->excerpt ?: Str::limit(strip_tags($post->body), 160) }}@endpushonce

@pushonce('og_tags')
{{-- OpenGraph Meta Tags --}}
<meta property="og:title" content="{{ $post->seo_title ?: $post->title }}">
<meta property="og:description" content="{{ $post->seo_description ?: $post->excerpt ?: Str::limit(strip_tags($post->body), 160) }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $post->og_image_url ?: $post->featured_image_url ?: asset('images/default-og.jpg') }}">
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
<meta property="article:author" content="{{ $post->author?->name ?: config('app.name', 'MYSHOP') }}">
<meta property="article:section" content="{{ $post->category?->name ?: 'Nội Thất' }}">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $post->seo_title ?: $post->title }}">
<meta name="twitter:description" content="{{ $post->seo_description ?: $post->excerpt ?: Str::limit(strip_tags($post->body), 160) }}">
<meta name="twitter:image" content="{{ $post->og_image_url ?: $post->featured_image_url ?: asset('images/default-og.jpg') }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $post->canonical_url ?: url()->current() }}">

{{-- Schema.org Article / BlogPosting JSON-LD --}}
@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => $post->schema_type ?: 'BlogPosting',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => url()->current(),
        ],
        'headline' => $post->title,
        'description' => $post->seo_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->body), 160)),
        'image' => [
            $post->featured_image_url ?: asset('images/default-og.jpg'),
        ],
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            'name' => $post->author?->name ?: 'MYSHOP Editorial',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'MYSHOP',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
            ],
        ],
    ];

    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang Chủ',
            'item' => route('home'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Blog',
            'item' => route('blog.index'),
        ],
    ];

    if ($post->category) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->category->name,
            'item' => route('blog.index', ['category' => $post->category->slug]),
        ];
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 4,
            'name' => $post->title,
            'item' => url()->current(),
        ];
    } else {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => url()->current(),
        ];
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

{{-- Schema.org BreadcrumbList JSON-LD --}}
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

{{-- Schema.org FAQPage JSON-LD (Conditional) --}}
@if(!empty($post->faq_schema) && is_array($post->faq_schema) && count($post->faq_schema) > 0)
@php
    $faqEntities = [];
    foreach ($post->faq_schema as $faq) {
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $faq['question'] ?? $faq['q'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'] ?? $faq['a'] ?? '',
            ],
        ];
    }
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqEntities,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endpushonce

@section('content')
{{-- Admin Preview Warning Banner --}}
@if($post->status !== 'published' || ($post->published_at && $post->published_at->isFuture()))
    <div class="bg-amber-500 text-white py-2 px-4 text-center text-xs font-medium uppercase tracking-widest sticky top-[72px] z-40">
        ⚠️ Chế độ xem trước (Bài viết chưa công khai hoặc đang ở trạng thái: {{ $post->status }})
    </div>
@endif

{{-- Breadcrumbs & Post Hero Header --}}
<div class="bg-[#F0F0F0] py-8 md:py-12 border-b border-[#E5E5E5]">
    <div class="section-wrapper">
        {{-- Breadcrumbs --}}
        <nav class="flex flex-wrap items-center gap-2 text-xs text-[#888888] font-light mb-6 uppercase tracking-wider" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#23232C] transition-colors">Trang Chủ</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-[#23232C] transition-colors">Blog</a>
            @if($post->category)
                <span>/</span>
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-[#23232C] transition-colors">
                    {{ $post->category->name }}
                </a>
            @endif
            <span>/</span>
            <span class="text-[#23232C] font-normal truncate max-w-xs md:max-w-md">{{ $post->title }}</span>
        </nav>

        <div class="max-w-4xl">
            @if($post->category)
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                   class="inline-block px-3 py-1 bg-white text-[#23232C] text-[10px] font-semibold tracking-[0.2em] uppercase border border-[#E5E5E5] mb-4 hover:bg-[#23232C] hover:text-white transition-colors">
                    {{ $post->category->name }}
                </a>
            @endif

            <h1 class="text-3xl md:text-5xl font-light text-[#23232C] leading-tight mb-6">
                {{ $post->title }}
            </h1>

            {{-- Meta Row & Social Share --}}
            <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-[#E5E5E5]">
                <div class="flex items-center gap-3 text-xs text-[#888888] font-light">
                    <div class="w-8 h-8 rounded-full bg-[#23232C] text-white flex items-center justify-center font-medium text-xs">
                        {{ strtoupper(substr($post->author?->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-[#23232C]">{{ $post->author?->name ?? 'MYSHOP Editorial' }}</p>
                        <p class="text-[11px]">{{ $post->published_at?->format('d/m/Y') }} • {{ $post->reading_time_minutes }} phút đọc</p>
                    </div>
                </div>

                {{-- Social Share Buttons --}}
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-medium tracking-[0.15em] uppercase text-[#888888]">Chia Sẻ:</span>
                    {{-- Facebook --}}
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="w-8 h-8 rounded-full bg-white border border-[#E5E5E5] flex items-center justify-center text-[#888888] hover:text-[#23232C] hover:border-[#23232C] transition-colors"
                       aria-label="Chia sẻ lên Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    {{-- Twitter / X --}}
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="w-8 h-8 rounded-full bg-white border border-[#E5E5E5] flex items-center justify-center text-[#888888] hover:text-[#23232C] hover:border-[#23232C] transition-colors"
                       aria-label="Chia sẻ lên X">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    {{-- Copy Link Button --}}
                    <button type="button"
                            @click="navigator.clipboard.writeText(window.location.href); $dispatch('toast', { message: 'Đã sao chép liên kết vào bộ nhớ tạm!', type: 'success' })"
                            class="w-8 h-8 rounded-full bg-white border border-[#E5E5E5] flex items-center justify-center text-[#888888] hover:text-[#23232C] hover:border-[#23232C] transition-colors"
                            aria-label="Sao chép liên kết bài viết">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.364-3.182l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Featured Banner Image --}}
@if($post->banner_image_url || $post->featured_image_url)
    <div class="section-wrapper py-6 md:py-10">
        <div class="relative aspect-[21/9] md:aspect-[21/9] overflow-hidden bg-[#E8E4DF] border border-[#E5E5E5]">
            <img src="{{ $post->banner_image_url ?: $post->featured_image_url }}"
                 alt="{{ $post->title }}"
                 class="w-full h-full object-cover">
        </div>
    </div>
@endif

{{-- Main Reading Layout (2 Columns) --}}
<div class="section-wrapper py-10 md:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        {{-- MAIN COLUMN: Article Body & Bottom Contextual Showcase (8 Cols) --}}
        <main class="lg:col-span-8 flex flex-col">

            {{-- Inline / Mobile Table of Contents (Shown on mobile/tablet or top of body) --}}
            @if(!empty($toc))
                <div class="lg:hidden mb-10 p-6 bg-white border border-[#E5E5E5]" x-data="{ open: true }">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] flex items-center gap-2">
                            <span>Mục Lục Bài Viết</span>
                        </h3>
                        <span x-text="open ? '−' : '+'" class="text-base text-[#888888]"></span>
                    </div>
                    <nav x-show="open" class="mt-4 pt-4 border-t border-[#E5E5E5] flex flex-col gap-2.5" aria-label="Mục lục bài viết di động">
                        @foreach($toc as $item)
                            <a href="#{{ $item['id'] }}"
                               class="text-xs text-[#888888] hover:text-[#23232C] hover:underline transition-colors {{ $item['level'] === 3 ? 'pl-4' : 'font-medium text-[#23232C]' }}">
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif

            {{-- Excerpt Highlight --}}
            @if($post->excerpt)
                <div class="p-6 md:p-8 bg-white border-l-4 border-[#23232C] mb-10 text-base md:text-lg font-light text-[#23232C] leading-relaxed italic">
                    {{ $post->excerpt }}
                </div>
            @endif

            {{-- Main Article Prose Body --}}
            <article class="prose prose-lg max-w-none text-[#23232C] font-light leading-relaxed
                            prose-headings:font-normal prose-headings:text-[#23232C] prose-headings:tracking-wide prose-headings:mt-8 prose-headings:mb-4
                            prose-h2:text-2xl md:prose-h2:text-3xl prose-h2:border-b prose-h2:border-[#E5E5E5] prose-h2:pb-3
                            prose-h3:text-xl md:prose-h3:text-2xl
                            prose-p:text-[#23232C] prose-p:leading-relaxed prose-p:mb-6
                            prose-a:text-[#23232C] prose-a:underline prose-a:underline-offset-4 hover:prose-a:opacity-70
                            prose-img:rounded-none prose-img:border prose-img:border-[#E5E5E5] prose-img:my-8
                            prose-blockquote:border-l-2 prose-blockquote:border-[#23232C] prose-blockquote:italic prose-blockquote:font-light prose-blockquote:pl-6
                            prose-ul:list-disc prose-ol:list-decimal prose-li:my-1">
                {!! $anchoredBody !!}
            </article>

            {{-- FAQ Section (If faq_schema is provided) --}}
            @if(!empty($post->faq_schema) && is_array($post->faq_schema) && count($post->faq_schema) > 0)
                <section class="mt-14 pt-10 border-t border-[#E5E5E5]" aria-label="Câu hỏi thường gặp">
                    <h2 class="text-xl md:text-2xl font-light text-[#23232C] mb-6">
                        Câu Hỏi Thường Gặp (FAQ)
                    </h2>
                    <div class="space-y-4">
                        @foreach($post->faq_schema as $faq)
                            <div class="bg-white border border-[#E5E5E5] p-5" x-data="{ open: false }">
                                <button type="button"
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between text-left font-medium text-sm text-[#23232C] gap-4">
                                    <span>{{ $faq['question'] ?? $faq['q'] ?? '' }}</span>
                                    <span x-text="open ? '−' : '+'" class="text-base text-[#888888] shrink-0"></span>
                                </button>
                                <div x-show="open" class="mt-3 pt-3 border-t border-[#E5E5E5] text-xs text-[#888888] font-light leading-relaxed">
                                    {{ $faq['answer'] ?? $faq['a'] ?? '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Post Footer Tags & Share Bar --}}
            <div class="mt-14 pt-8 border-t border-[#E5E5E5] flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    @if($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                           class="filter-pill">
                            #{{ $post->category->name }}
                        </a>
                    @endif
                </div>

                {{-- Share Actions --}}
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-medium tracking-[0.15em] uppercase text-[#888888]">Chia Sẻ:</span>
                    <button type="button"
                            @click="navigator.clipboard.writeText(window.location.href); $dispatch('toast', { message: 'Đã sao chép liên kết vào bộ nhớ tạm!', type: 'success' })"
                            class="px-4 py-2 bg-white border border-[#E5E5E5] text-xs uppercase tracking-wider text-[#23232C] hover:bg-[#23232C] hover:text-white transition-colors">
                        Sao Chép Link
                    </button>
                </div>
            </div>

            {{-- ════════════════════════════════════════
                 CONTEXTUAL COMMERCE BOTTOM SHOWCASE
                 ════════════════════════════════════════ --}}
            @if($post->products->isNotEmpty())
                <section class="mt-16 pt-12 border-t-2 border-[#23232C]" aria-label="Sản phẩm trong bài viết">
                    <div class="mb-8">
                        <p class="text-[10px] font-semibold tracking-[0.25em] uppercase text-[#888888] mb-1">Contextual Commerce</p>
                        <h2 class="text-2xl md:text-3xl font-light text-[#23232C]">
                            Sản Phẩm Trong Bài Viết
                        </h2>
                        <p class="text-xs text-[#888888] font-light mt-1">
                            Các sản phẩm nội thất xuất hiện và được gợi ý bài trí trong bài viết này.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($post->products as $product)
                            <article class="bg-white border border-[#E5E5E5] flex flex-col group hover:shadow-md transition-shadow duration-300" aria-label="{{ $product->name }}">
                                <div class="relative aspect-square overflow-hidden bg-[#E8E4DF]">
                                    <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                                        @if($product->primary_image_url)
                                            <img src="{{ $product->primary_image_url }}"
                                                 alt="{{ $product->name }}"
                                                 loading="lazy"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#e0dbd5] to-[#ccc6bf]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#b0a89e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="p-4 flex flex-col flex-1 justify-between text-center">
                                    <div>
                                        @if($product->category)
                                            <p class="text-[9px] font-semibold text-[#888888] uppercase tracking-[0.2em] mb-1">
                                                {{ $product->category->name }}
                                            </p>
                                        @endif
                                        <h3 class="text-xs font-normal text-[#23232C] line-clamp-1 mb-2">
                                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-[#666666] transition-colors">
                                                {{ $product->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm font-medium text-[#23232C] mb-4">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </p>
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="w-full py-2.5 bg-[#23232C] text-white text-[10px] font-medium tracking-[0.2em] uppercase hover:bg-black transition-colors block">
                                        Xem Chi Tiết
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

        </main>

        {{-- SIDEBAR COLUMN (4 Cols, Sticky) --}}
        <aside class="lg:col-span-4 space-y-8 sticky top-24">

            {{-- 1. Desktop Table of Contents --}}
            @if(!empty($toc))
                <div class="hidden lg:block bg-white border border-[#E5E5E5] p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] pb-3 border-b border-[#E5E5E5] mb-4 flex items-center gap-2">
                        <span>Mục Lục Bài Viết</span>
                    </h3>
                    <nav class="flex flex-col gap-2.5 max-h-[360px] overflow-y-auto pr-2" aria-label="Mục lục bài viết">
                        @foreach($toc as $item)
                            <a href="#{{ $item['id'] }}"
                               class="text-xs text-[#888888] hover:text-[#23232C] hover:underline transition-colors leading-relaxed {{ $item['level'] === 3 ? 'pl-4' : 'font-medium text-[#23232C]' }}">
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif

            {{-- 2. Contextual Primary Product Sidebar Card --}}
            @if($post->products->isNotEmpty())
                @php $primaryProduct = $post->products->first(); @endphp
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E5E5E5] mb-4">
                        <span class="text-[9px] font-semibold tracking-[0.2em] uppercase text-[#888888]">Gợi Ý Trong Bài</span>
                        <span class="px-2 py-0.5 bg-[#23232C] text-white text-[9px] font-semibold tracking-widest uppercase">
                            Featured
                        </span>
                    </div>

                    <div class="relative aspect-square overflow-hidden bg-[#E8E4DF] mb-4">
                        <a href="{{ route('products.show', $primaryProduct->slug) }}">
                            @if($primaryProduct->primary_image_url)
                                <img src="{{ $primaryProduct->primary_image_url }}"
                                     alt="{{ $primaryProduct->name }}"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#E8E4DF]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#888888]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                        </a>
                    </div>

                    <h4 class="text-sm font-normal text-[#23232C] mb-1">
                        <a href="{{ route('products.show', $primaryProduct->slug) }}" class="hover:text-[#666666]">
                            {{ $primaryProduct->name }}
                        </a>
                    </h4>

                    <div class="flex items-center justify-between mb-4">
                        <p class="text-base font-semibold text-[#23232C]">
                            {{ number_format($primaryProduct->price, 0, ',', '.') }}₫
                        </p>
                        <span class="text-[10px] {{ ($primaryProduct->stock ?? 0) > 0 ? 'text-green-700 font-medium' : 'text-red-600' }}">
                            {{ ($primaryProduct->stock ?? 0) > 0 ? '● Còn hàng' : 'Hết hàng' }}
                        </span>
                    </div>

                    <a href="{{ route('products.show', $primaryProduct->slug) }}"
                       class="w-full py-3 bg-[#23232C] text-white text-[10px] font-medium tracking-[0.2em] uppercase hover:bg-black transition-colors block text-center">
                        Xem Sản Phẩm &rarr;
                    </a>
                </div>
            @endif

            {{-- 3. Related Articles Widget --}}
            @if($relatedPosts->isNotEmpty())
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] pb-3 border-b border-[#E5E5E5] mb-4">
                        Bài Viết Cùng Chủ Đề
                    </h3>
                    <div class="space-y-4">
                        @foreach($relatedPosts as $related)
                            <article class="flex items-start gap-3 group" aria-label="{{ $related->title }}">
                                <div class="w-20 h-16 shrink-0 bg-[#E8E4DF] overflow-hidden">
                                    <a href="{{ route('blog.show', $related->slug) }}">
                                        @if($related->featured_image_url)
                                            <img src="{{ $related->featured_image_url }}"
                                                 alt="{{ $related->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-normal text-[#23232C] line-clamp-2 leading-snug group-hover:text-[#666666] transition-colors mb-1">
                                        <a href="{{ route('blog.show', $related->slug) }}">
                                            {{ $related->title }}
                                        </a>
                                    </h4>
                                    <p class="text-[10px] text-[#888888] font-light">
                                        {{ $related->published_at?->format('d/m/Y') }} • {{ $related->reading_time_minutes }}p
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

        </aside>

    </div>
</div>
@endsection
