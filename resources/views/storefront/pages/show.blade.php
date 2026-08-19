@extends('layouts.storefront')

@pushonce('page_title'){{ $page->seo_title ?: $page->title }} — @endpushonce
@pushonce('meta_description'){{ $page->seo_description ?: $page->excerpt ?: Str::limit(strip_tags($page->body), 160) }}@endpushonce

@pushonce('og_tags')
<meta property="og:title" content="{{ $page->seo_title ?: $page->title }}">
<meta property="og:description" content="{{ $page->seo_description ?: $page->excerpt ?: Str::limit(strip_tags($page->body), 160) }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $page->og_image_url ?: $page->featured_image_url ?: asset('images/default-og.jpg') }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $page->seo_title ?: $page->title }}">
<meta name="twitter:description" content="{{ $page->seo_description ?: $page->excerpt ?: Str::limit(strip_tags($page->body), 160) }}">
<meta name="twitter:image" content="{{ $page->og_image_url ?: $page->featured_image_url ?: asset('images/default-og.jpg') }}">

<link rel="canonical" href="{{ $page->canonical_url ?: url()->current() }}">

{{-- Schema.org WebPage JSON-LD --}}
@php
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => $page->schema_type ?: 'WebPage',
        'name' => $page->title,
        'description' => $page->seo_description ?: ($page->excerpt ?: Str::limit(strip_tags($page->body), 160)),
        'url' => url()->current(),
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'MYSHOP',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
            ],
        ],
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Trang Chủ',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $page->title,
                'item' => url()->current(),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

{{-- Schema.org BreadcrumbList JSON-LD --}}
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

{{-- Schema.org FAQPage JSON-LD (Conditional) --}}
@if(!empty($page->faq_schema) && is_array($page->faq_schema) && count($page->faq_schema) > 0)
@php
    $faqEntities = [];
    foreach ($page->faq_schema as $faq) {
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $faq['question'] ?? $faq['q'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'] ?? $faq['a'] ?? '',
            ],
        ];
    }
    $pageFaqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqEntities,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($pageFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endpushonce

@section('content')
<div class="bg-[#F0F0F0] py-8 md:py-12 border-b border-[#E5E5E5]">
    <div class="section-wrapper">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-xs text-[#888888] font-light mb-6 uppercase tracking-wider" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#23232C] transition-colors">Trang Chủ</a>
            <span>/</span>
            <span class="text-[#23232C] font-normal">{{ $page->title }}</span>
        </nav>

        <div class="max-w-4xl">
            @if($page->template === 'policy')
                <span class="inline-block px-3 py-1 bg-white text-[#23232C] text-[10px] font-semibold tracking-[0.2em] uppercase border border-[#E5E5E5] mb-3">
                    Chính Sách &amp; Điều Khoản
                </span>
            @endif

            <h1 class="text-3xl md:text-5xl font-light text-[#23232C] leading-tight mb-4">
                {{ $page->title }}
            </h1>

            <p class="text-xs text-[#888888] font-light">
                Cập nhật lần cuối: {{ $page->updated_at->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>

{{-- Page Featured Banner if available --}}
@if($page->featured_image_url)
    <div class="section-wrapper py-8">
        <div class="relative aspect-[21/9] overflow-hidden bg-[#E8E4DF] border border-[#E5E5E5]">
            <img src="{{ $page->featured_image_url }}"
                 alt="{{ $page->title }}"
                 class="w-full h-full object-cover">
        </div>
    </div>
@endif

{{-- Page Body Content --}}
<div class="section-wrapper py-10 md:py-16">
    <div class="{{ $page->template === 'full_width' ? 'w-full' : 'max-w-4xl mx-auto' }} bg-white border border-[#E5E5E5] p-8 md:p-14 shadow-sm">
        @if($page->excerpt)
            <div class="p-6 bg-[#F9FAFB] border-l-4 border-[#23232C] mb-10 text-sm md:text-base font-light text-[#23232C] leading-relaxed italic">
                {{ $page->excerpt }}
            </div>
        @endif

        <article class="prose prose-lg max-w-none text-[#23232C] font-light leading-relaxed
                        prose-headings:font-normal prose-headings:text-[#23232C] prose-headings:tracking-wide prose-headings:mt-8 prose-headings:mb-4
                        prose-h2:text-2xl md:prose-h2:text-3xl prose-h2:border-b prose-h2:border-[#E5E5E5] prose-h2:pb-3
                        prose-h3:text-xl md:prose-h3:text-2xl
                        prose-p:text-[#23232C] prose-p:leading-relaxed prose-p:mb-6
                        prose-a:text-[#23232C] prose-a:underline prose-a:underline-offset-4 hover:prose-a:opacity-70
                        prose-img:rounded-none prose-img:border prose-img:border-[#E5E5E5]
                        prose-blockquote:border-l-2 prose-blockquote:border-[#23232C] prose-blockquote:italic prose-blockquote:font-light
                        prose-ul:list-disc prose-ol:list-decimal prose-li:my-1">
            {!! $page->body !!}
        </article>

        {{-- FAQ Section for Pages (If present) --}}
        @if(!empty($page->faq_schema) && is_array($page->faq_schema) && count($page->faq_schema) > 0)
            <section class="mt-14 pt-10 border-t border-[#E5E5E5]" aria-label="Câu hỏi thường gặp">
                <h2 class="text-xl md:text-2xl font-light text-[#23232C] mb-6">
                    Câu Hỏi Thường Gặp
                </h2>
                <div class="space-y-4">
                    @foreach($page->faq_schema as $faq)
                        <div class="bg-[#F9FAFB] border border-[#E5E5E5] p-5" x-data="{ open: false }">
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
    </div>
</div>
@endsection
