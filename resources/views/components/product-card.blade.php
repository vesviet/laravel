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
            <span class="bg-badge-hot text-white font-bold text-[10px] tracking-wider uppercase px-2.5 py-0.5 rounded-full shadow-sm"
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
               class="p-2 rounded-full bg-white/90 hover:bg-white text-muted-text hover:text-badge-hot shadow-sm transition-colors flex items-center justify-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-dark"
               aria-label="Thêm {{ $product->name }} vào danh sách yêu thích">
                <x-icons.heart stroke="1.75" />
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
                    <x-icons.image-placeholder class="w-10 h-10 text-[#b0a89e] group-hover:scale-110 transition-transform duration-500 ease-out" />
                </div>
            @endif

            {{-- "Xem Chi Tiết" Quick Action Hover Bar --}}
            <div class="absolute bottom-0 inset-x-0 bg-primary-dark/90 text-white text-[10px] font-medium tracking-[0.2em] uppercase text-center py-3.5 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
                Xem Chi Tiết
            </div>
        </a>
    </div>

    {{-- Clean Typography Meta Information --}}
    <div class="mt-3 text-center px-1">
        {{-- Category in small uppercase tracking --}}
        @if($product->category)
            <p class="text-[10px] font-medium text-muted-text tracking-[0.2em] uppercase mb-1">
                {{ $product->category->name }}
            </p>
        @endif

        {{-- Product Title in medium font with subtle hover transition --}}
        <h3 class="text-sm font-normal md:font-medium text-primary-dark leading-snug mb-1">
            <a href="{{ route('products.show', $product->slug) }}"
               class="block hover:opacity-60 transition-opacity line-clamp-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-dark rounded-sm">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Formatted VND Price: Strike-Through & Promoted Price --}}
        @if($promoted)
            <div class="flex items-center justify-center gap-2">
                <span class="text-sm font-bold text-badge-hot">
                    {{ number_format($promoted->promotedPrice, 0, ',', '.') }}₫
                </span>
                <span class="line-through text-gray-400 text-xs sm:text-sm">
                    {{ number_format($promoted->originalPrice, 0, ',', '.') }}₫
                </span>
            </div>
        @else
            <p class="text-sm font-medium text-primary-dark">
                {{ number_format($product->price, 0, ',', '.') }}₫
            </p>
        @endif
    </div>

</article>
