@extends('layouts.app')
@section('title', ($article->meta_title ?: $article->title) . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $article->meta_description ?: $article->excerpt)
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <nav style="font-size:0.875rem;color:var(--color-gray-500);margin-bottom:2rem;">
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('articles.index') }}">Tin tức</a> /
            <span style="color:var(--color-navy);">{{ Str::limit($article->title, 50) }}</span>
        </nav>

        @if($article->category)
            <div class="section-badge">{{ $article->category->name }}</div>
        @endif
        <h1 style="font-size:var(--font-size-3xl);font-weight:800;margin:0.75rem 0 1rem;">{{ $article->title }}</h1>
        <p style="font-size:0.875rem;color:var(--color-gray-500);margin-bottom:2rem;">
            {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
        </p>

        @if($article->getFirstMediaUrl('featured_image', 'banner'))
            <div style="border-radius:var(--radius-xl);overflow:hidden;margin-bottom:2rem;">
                <img src="{{ $article->getFirstMediaUrl('featured_image', 'banner') }}" alt="{{ $article->title }}" style="width:100%;">
            </div>
        @endif

        <div style="line-height:1.8;color:var(--color-gray-700);">
            {!! $article->content !!}
        </div>

        @if($relatedArticles->count())
        <div style="margin-top:4rem;padding-top:2rem;border-top:1px solid var(--color-gray-200);">
            <h2 style="font-size:var(--font-size-xl);font-weight:700;margin-bottom:1.5rem;">Bài viết liên quan</h2>
            <div class="product-grid" style="grid-template-columns:repeat(3,1fr);">
                @foreach($relatedArticles as $ra)
                    <div class="product-card">
                        <a href="{{ route('articles.show', $ra->slug) }}">
                            <div class="product-card-image" style="aspect-ratio:16/10;">
                                @if($ra->getFirstMediaUrl('featured_image', 'thumb'))
                                    <img src="{{ $ra->getFirstMediaUrl('featured_image', 'thumb') }}" alt="{{ $ra->title }}" loading="lazy">
                                @endif
                            </div>
                        </a>
                        <div class="product-card-body">
                            <h3 class="product-card-name"><a href="{{ route('articles.show', $ra->slug) }}">{{ $ra->title }}</a></h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
