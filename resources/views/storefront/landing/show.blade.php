@extends('layouts.storefront')

@section('title', $page->title)

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($page->content), 160) }}">
    <meta property="og:title" content="{{ $page->title }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($page->content), 160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
@endsection

@section('content')
    <div class="landing-page-container">
        @if(is_array($content = json_decode($page->content, true)))
            {{-- Handle structured customizable sections if content is JSON --}}
            @foreach($content as $section)
                @if(isset($section['type']))
                    @includeIf('storefront.landing.sections.' . $section['type'], ['data' => $section['data'] ?? []])
                @endif
            @endforeach
        @else
            {{-- Fallback for standard HTML content --}}
            <div class="prose max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                {!! $page->content !!}
            </div>
        @endif
    </div>
@endsection
