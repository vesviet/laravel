@extends('layouts.storefront')

@pushonce('page_title')Blog & Kiến Thức Nội Thất — @endpushonce
@pushonce('meta_description')Khám phá các bài viết kiến thức, xu hướng bài trí không gian sống và cẩm nang bảo quản nội thất Bắc Âu Scandinavian hiện đại tại MYSHOP.@endpushonce

@pushonce('og_tags')
<meta property="og:title" content="Blog & Kiến Thức Nội Thất | MYSHOP">
<meta property="og:description" content="Khám phá các bài viết kiến thức, xu hướng bài trí không gian sống và cẩm nang bảo quản nội thất Bắc Âu Scandinavian hiện đại tại MYSHOP.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('blog.index') }}">
<meta property="og:image" content="{{ asset('images/default-og.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Blog & Kiến Thức Nội Thất | MYSHOP">
<meta name="twitter:description" content="Khám phá các bài viết kiến thức, xu hướng bài trí không gian sống và cẩm nang bảo quản nội thất Bắc Âu Scandinavian hiện đại tại MYSHOP.">
<meta name="twitter:image" content="{{ asset('images/default-og.jpg') }}">
<link rel="canonical" href="{{ route('blog.index') }}">
@endpushonce

@section('content')
<div class="bg-surface-bg py-8 md:py-12 border-b border-border-subtle">
    <div class="section-wrapper">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-xs text-muted-text font-light mb-6 uppercase tracking-wider" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-primary-dark transition-colors">Trang Chủ</a>
            <span>/</span>
            <span class="text-primary-dark font-normal">Blog &amp; Kiến Thức</span>
        </nav>

        {{-- Section Hero Header --}}
        <div class="max-w-3xl">
            <p class="text-[10px] font-semibold tracking-[0.25em] uppercase text-muted-text mb-2">Editorial & Knowledge Hub</p>
            <h1 class="text-3xl md:text-5xl font-light text-primary-dark leading-tight mb-4">
                Blog &amp; Kiến Thức Nội Thất
            </h1>
            <p class="text-sm md:text-base text-muted-text font-light leading-relaxed">
                Khám phá xu hướng thiết kế, nghệ thuật bài trí không gian sống và cẩm nang bảo quản nội thất chuẩn Scandinavian.
            </p>
        </div>
    </div>
</div>

<div class="section-wrapper py-10 md:py-16">
    {{-- Filter & Search Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-border-subtle mb-10">
        {{-- Category Pills --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('blog.index', array_filter(['search' => request('search')])) }}"
               class="px-4 py-2 text-xs font-medium uppercase tracking-[0.15em] border transition-all duration-200 {{ !request('category') ? 'bg-primary-dark text-white border-primary-dark' : 'bg-white text-primary-dark border-border-subtle hover:border-primary-dark' }}">
                Tất Cả
            </a>
            @foreach($categories as $cat)
                @php
                    $isActive = request('category') === $cat->slug || request('category') === (string)$cat->id;
                @endphp
                <a href="{{ route('blog.index', array_merge(array_filter(['search' => request('search')]), ['category' => $cat->slug])) }}"
                   class="px-4 py-2 text-xs font-medium uppercase tracking-[0.15em] border transition-all duration-200 flex items-center gap-1.5 {{ $isActive ? 'bg-primary-dark text-white border-primary-dark' : 'bg-white text-primary-dark border-border-subtle hover:border-primary-dark' }}">
                    <span>{{ $cat->name }}</span>
                    <span class="text-[10px] opacity-70">({{ $cat->posts_count }})</span>
                </a>
            @endforeach
        </div>

        {{-- Keyword Search Form --}}
        <form action="{{ route('blog.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-80">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative w-full">
                <input type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Tìm kiếm bài viết..."
                       class="w-full bg-white border border-border-subtle px-4 py-2 pr-10 text-xs font-light text-primary-dark placeholder-muted-text focus:outline-none focus:border-primary-dark">
                <button type="submit"
                        class="absolute right-0 top-0 bottom-0 px-3 flex items-center justify-center text-muted-text hover:text-primary-dark"
                        aria-label="Tìm kiếm">
                    <x-icons.search class="w-4 h-4" />
                </button>
            </div>
            @if(request('search') || request('category'))
                <a href="{{ route('blog.index') }}"
                   class="shrink-0 px-3 py-2 text-xs text-muted-text hover:text-primary-dark border border-border-subtle bg-white transition-colors"
                   title="Xóa bộ lọc">
                    ✕
                </a>
            @endif
        </form>
    </div>

    {{-- Featured Hero Article (Only on page 1 without active search) --}}
    @if($featuredPost && !request('search') && (!request('page') || request('page') == 1))
        <section class="mb-14" aria-label="Bài viết nổi bật">
            <div class="bg-white border border-border-subtle overflow-hidden group grid grid-cols-1 lg:grid-cols-12 gap-0">
                <div class="lg:col-span-7 relative aspect-[16/10] lg:aspect-auto overflow-hidden bg-[#E8E4DF]">
                    <a href="{{ route('blog.show', $featuredPost->slug) }}" class="block w-full h-full">
                        @if($featuredPost->featured_image_url)
                            <img src="{{ $featuredPost->featured_image_url }}"
                                 alt="{{ $featuredPost->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#e0dbd5] to-[#ccc6bf] min-h-[300px]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#b0a89e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-4 left-4 z-10 px-3 py-1 bg-primary-dark text-white text-[9px] font-semibold tracking-[0.2em] uppercase">
                            Nổi Bật
                        </span>
                    </a>
                </div>

                <div class="lg:col-span-5 p-8 md:p-12 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 text-xs text-muted-text font-light mb-3">
                            @if($featuredPost->category)
                                <span class="font-medium text-primary-dark uppercase tracking-wider text-[10px]">
                                    {{ $featuredPost->category->name }}
                                </span>
                                <span>•</span>
                            @endif
                            <span>{{ $featuredPost->published_at?->format('d/m/Y') }}</span>
                            <span>•</span>
                            <span>{{ $featuredPost->reading_time_minutes }} phút đọc</span>
                        </div>

                        <h2 class="text-2xl md:text-3xl font-light text-primary-dark leading-snug mb-4">
                            <a href="{{ route('blog.show', $featuredPost->slug) }}" class="group-hover:text-[#666666] transition-colors">
                                {{ $featuredPost->title }}
                            </a>
                        </h2>

                        <p class="text-sm text-muted-text font-light leading-relaxed line-clamp-3 mb-6">
                            {{ $featuredPost->excerpt ?: Str::limit(strip_tags($featuredPost->body), 180) }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-border-subtle flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-primary-dark">
                                {{ $featuredPost->author?->name ?? 'MYSHOP Editorial' }}
                            </span>
                        </div>
                        <a href="{{ route('blog.show', $featuredPost->slug) }}"
                           class="text-xs font-semibold uppercase tracking-[0.15em] text-primary-dark group-hover:underline flex items-center gap-1">
                            <span>Đọc Bài Viết</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Articles Grid --}}
    @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white border border-border-subtle flex flex-col group hover:shadow-md transition-shadow duration-300" aria-label="{{ $post->title }}">
                    {{-- Thumbnail Frame (16:9) --}}
                    <div class="relative aspect-[16/9] overflow-hidden bg-[#E8E4DF]">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block w-full h-full">
                            @if($post->featured_image_url)
                                <img src="{{ $post->featured_image_url }}"
                                     alt="{{ $post->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#e0dbd5] to-[#ccc6bf]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#b0a89e]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            @if($post->category)
                                <span class="absolute top-3 left-3 z-10 px-2.5 py-1 bg-white/95 text-primary-dark text-[9px] font-semibold tracking-[0.15em] uppercase shadow-sm">
                                    {{ $post->category->name }}
                                </span>
                            @endif
                        </a>
                    </div>

                    {{-- Content Box --}}
                    <div class="p-6 flex flex-col flex-1 justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-[11px] text-muted-text font-light mb-2.5">
                                <span>{{ $post->published_at?->format('d/m/Y') }}</span>
                                <span>•</span>
                                <span>{{ $post->reading_time_minutes }} phút đọc</span>
                            </div>

                            <h3 class="text-lg font-normal text-primary-dark leading-snug mb-3 line-clamp-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="group-hover:text-[#666666] transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-muted-text font-light leading-relaxed line-clamp-3 mb-6">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->body), 120) }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-border-subtle flex items-center justify-between text-xs">
                            <span class="text-muted-text font-light text-[11px]">
                                {{ $post->author?->name ?? 'MYSHOP' }}
                            </span>
                            <a href="{{ route('blog.show', $post->slug) }}"
                               class="font-semibold uppercase tracking-[0.15em] text-primary-dark group-hover:underline flex items-center gap-1 text-[11px]">
                                <span>Đọc tiếp</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        {{-- Zero-State --}}
        <div class="text-center py-16 bg-white border border-border-subtle p-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-muted-text mb-4 stroke-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <h3 class="text-lg font-light text-primary-dark mb-2">Không tìm thấy bài viết nào phù hợp với bộ lọc.</h3>
            <p class="text-xs text-muted-text font-light mb-6">Hãy thử tìm kiếm với từ khóa khác hoặc bỏ các tiêu chí lọc hiện tại.</p>
            <a href="{{ route('blog.index') }}" class="btn-dark inline-block">
                Xem Tất Cả Bài Viết
            </a>
        </div>
    @endif
</div>
@endsection
