@props(['product'])

<article class="group relative" aria-label="{{ $product->name }}">

    {{-- HOT badge (is_featured) --}}
    @if($product->is_featured)
        <span class="badge-hot absolute top-2 left-2 z-10" aria-label="Sản phẩm nổi bật">HOT</span>
    @endif

    {{-- Wishlist button (top-right, show on hover) --}}
    <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
        @auth('customer')
            <livewire:wishlist-button :product="$product" :key="'wb-'.$product->id" />
        @endauth
    </div>

    {{-- Product image --}}
    <a href="{{ route('products.show', $product->slug) }}"
       class="block relative overflow-hidden aspect-square bg-[#E8E4DF]"
       tabindex="-1"
       aria-hidden="true">

        @if($product->image_path)
            <img
                src="{{ Storage::url($product->image_path) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                width="400"
                height="400"
                class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 ease-out"
            >
        @else
            {{-- Placeholder gradient when no image --}}
            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#e0dbd5] to-[#ccc6bf]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#b0a89e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
        @endif

        {{-- "Xem Chi Tiết" hover overlay bar --}}
        <div class="absolute bottom-0 inset-x-0 bg-[#2c2c2c]/90 text-white text-[10px] font-medium tracking-[0.2em] uppercase text-center py-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
            Xem Chi Tiết
        </div>
    </a>

    {{-- Product info --}}
    <div class="mt-3 text-center px-1">
        <a href="{{ route('products.show', $product->slug) }}"
           class="block text-sm tracking-wide hover:opacity-60 transition-opacity leading-snug mb-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1a1a1a] rounded-sm">
            {{ $product->name }}
        </a>
        @if($product->category)
            <p class="text-[10px] text-[#888888] tracking-widest uppercase mb-1">{{ $product->category->name }}</p>
        @endif
        <p class="text-sm font-medium">
            {{ number_format($product->price, 0, ',', '.') }}₫
        </p>
    </div>

</article>
