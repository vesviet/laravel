@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $page->meta_description ?: '')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <div class="section-badge">FAQ</div>
            <h1 class="section-title">{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="section-desc">{{ $page->excerpt }}</p>
            @endif
        </div>

        <div class="page-content faq-content" style="line-height:1.8;color:var(--color-gray-700);">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endsection
