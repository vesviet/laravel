@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $page->meta_description ?: $page->excerpt ?: '')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
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
    </div>
</section>
@endsection
