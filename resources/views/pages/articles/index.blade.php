@extends('layouts.app')
@section('title', 'Tin tức - Máy Điện Giải Sài Gòn')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Blog</div>
            <h1 class="section-title">Tin Tức & Kiến Thức</h1>
        </div>
        <div class="product-grid" style="grid-template-columns: repeat(3, 1fr);">
            @forelse($articles as $article)
                <div class="product-card">
                    <a href="{{ route('articles.show', $article->slug) }}">
                        <div class="product-card-image" style="aspect-ratio:16/10;">
                            @if($article->getFirstMediaUrl('featured_image', 'banner'))
                                <img src="{{ $article->getFirstMediaUrl('featured_image', 'banner') }}" alt="{{ $article->title }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--color-gray-300);font-size:3rem;">📰</div>
                            @endif
                        </div>
                    </a>
                    <div class="product-card-body">
                        @if($article->category)
                            <div class="product-card-category">{{ $article->category->name }}</div>
                        @endif
                        <h2 class="product-card-name"><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h2>
                        @if($article->excerpt)
                            <p style="font-size:0.875rem;color:var(--color-gray-500);margin-top:0.5rem;">{{ Str::limit($article->excerpt, 120) }}</p>
                        @endif
                        <div style="font-size:0.75rem;color:var(--color-gray-500);margin-top:0.75rem;">
                            {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="grid-column:1/-1;text-align:center;color:var(--color-gray-500);padding:3rem;">Chưa có bài viết nào.</p>
            @endforelse
        </div>
        {{ $articles->links() }}
    </div>
</section>
@endsection
