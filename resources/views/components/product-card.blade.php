@props(['product'])

@php
    $primaryImage = $product->primary_image_url;
    $secondaryImage = $product->secondary_image_url;
    $hasSecondaryImage = !empty($secondaryImage) && $secondaryImage !== $primaryImage;

    // Resolve active catalog promotion strike price & badge (Cached in memory - 0 N+1 queries)
    $promoted = app(\App\Services\Promotions\PromotionEngine::class)->resolveProductPromotedPrice($product);
@endphp

<article class="group relative flex flex-col" aria-label="{{ $product->name }}">

    {{-- Badges Stack (Top-Left): Featured HOT --}}
    @if($product->is_featured)
        <div class="absolute top-2.5 left-2.5 z-10 pointer-events-none flex flex-col gap-1 items-start">
            <span class="badge-hot" aria-label="Sản phẩm nổi bật">HOT</span>
        </div>
    @endif

    {{-- Promotion Badge (Top-Right Corner as per M4 spec) --}}
    @if($promoted)
        <div class="absolute top-2.5 right-2.5 z-10 pointer-events-none">
            <span class="bg-[#E84444] text-white font-bold text-[10px] tracking-wider uppercase px-2.5 py-0.5 rounded-full shadow-sm"
                  aria-label="Khuyến mãi {{ round($promoted->discountPercentage) }}%">
                {{ $promoted->badgeLabel ?: '-' . round($promoted->discountPercentage) . '% PROMO' }}
            </span>
        </div>
    @endif

    {{-- Wishlist Toggle Icon (Positioned smoothly below promo badge or at top-right on hover) --}}
    <div class="absolute {{ $promoted ? 'top-9' : 'top-2.5' }} right-2.5 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-out">
        @auth('customer')
            <livewire:wishlist-button :product="$product" :key="'wb-'.$product->id" />
        @else
            <a href="{{ route('account.login') }}"
               class="p-2 rounded-full bg-white/90 hover:bg-white text-[#888888] hover:text-[#E84444] shadow-sm transition-colors flex items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#23232C]"
               aria-label="Thêm {{ $product->name }} vào danh sách yêu thích">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </a>
        @endauth
    </div>

    {{-- Image Frame with Dual-Image Hover Transition & Quick Action Bar --}}
    <div class="relative overflow-hidden aspect-square bg-[#E8E4DF]">
        <a href="{{ route('products.show', $product->slug) }}"
           class="block w-full h-full"
           tabindex="-1"
           aria-hidden="true">

            @if($primaryImage)
                {{-- Primary Image (visible by default) --}}
                <img
                    src="{{ $primaryImage }}"
                    alt="{{ $product->name }}"
                    loading="lazy"
                    width="400"
                    height="400"
                    class="w-full h-full object-cover transition-all duration-500 ease-out {{ $hasSecondaryImage ? 'group-hover:opacity-0 group-hover:scale-105' : 'group-hover:scale-[1.04]' }}"
                >

                {{-- Secondary Image (smooth fade & scale in on hover when available) --}}
                @if($hasSecondaryImage)
                    <img
                        src="{{ $secondaryImage }}"
                        alt="{{ $product->name }} — góc nhìn khác"
                        loading="lazy"
                        width="400"
                        height="400"
                        class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 scale-100 group-hover:scale-105 transition-all duration-500 ease-out"
                    >
                @endif
            @else
                {{-- Styled SVG Placeholder Fallback --}}
                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#e0dbd5] to-[#ccc6bf]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#b0a89e] group-hover:scale-110 transition-transform duration-500 ease-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
            @endif

            {{-- "Xem Chi Tiết" Quick Action Hover Bar --}}
            <div class="absolute bottom-0 inset-x-0 bg-[#23232C]/90 text-white text-[10px] font-medium tracking-[0.2em] uppercase text-center py-3.5 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
                Xem Chi Tiết
            </div>
        </a>
    </div>

    {{-- Clean Typography Meta Information --}}
    <div class="mt-3 text-center px-1">
        {{-- Category in small uppercase tracking --}}
        @if($product->category)
            <p class="text-[10px] font-medium text-[#888888] tracking-[0.2em] uppercase mb-1">
                {{ $product->category->name }}
            </p>
        @endif

        {{-- Product Title in medium font with subtle hover transition --}}
        <h3 class="text-sm font-normal md:font-medium text-[#23232C] leading-snug mb-1">
            <a href="{{ route('products.show', $product->slug) }}"
               class="block hover:opacity-60 transition-opacity line-clamp-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#23232C] rounded-sm">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Formatted VND Price: Strike-Through & Promoted Price --}}
        @if($promoted)
            <div class="flex items-center justify-center gap-2">
                <span class="text-sm font-bold text-[#E84444]">
                    {{ number_format($promoted->promotedPrice, 0, ',', '.') }}₫
                </span>
                <span class="line-through text-gray-400 text-xs sm:text-sm">
                    {{ number_format($promoted->originalPrice, 0, ',', '.') }}₫
                </span>
            </div>
        @else
            <p class="text-sm font-medium text-[#23232C]">
                {{ number_format($product->price, 0, ',', '.') }}₫
            </p>
        @endif
    </div>

</article>
