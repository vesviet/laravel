@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $page->meta_description ?: 'Máy Điện Giải Sài Gòn - Đơn vị phân phối máy điện giải nước ion kiềm chính hãng hàng đầu Việt Nam.')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <div class="section-badge">Về chúng tôi</div>
            <h1 class="section-title">{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="section-desc">{{ $page->excerpt }}</p>
            @endif
        </div>

        @if($page->getFirstMediaUrl('banner', 'banner'))
            <div style="margin-bottom:2rem;border-radius:var(--radius-lg);overflow:hidden;">
                <img src="{{ $page->getFirstMediaUrl('banner', 'banner') }}"
                     alt="{{ $page->title }}"
                     style="width:100%;height:auto;display:block;">
            </div>
        @endif

        <div class="page-content" style="line-height:1.8;color:var(--color-gray-700);">
            {!! $page->content !!}
        </div>

        <div class="features-grid" style="margin-top:3rem;">
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3 class="feature-title">10+ Năm</h3>
                <p class="feature-desc">Kinh nghiệm trong ngành máy điện giải</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👨‍👩‍👧‍👦</div>
                <h3 class="feature-title">5,000+</h3>
                <p class="feature-desc">Gia đình đã tin dùng sản phẩm</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">4.9/5</h3>
                <p class="feature-desc">Đánh giá từ khách hàng</p>
            </div>
        </div>
    </div>
</section>
@endsection
