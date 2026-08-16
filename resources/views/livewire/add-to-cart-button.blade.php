<div>
    {{-- Variant selector --}}
    @if($product->variants->count() > 0)
        <div class="mb-6">
            <label for="variant" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-3">
                Lựa Chọn
            </label>
            <div class="flex flex-wrap gap-2">
                @foreach($product->variants as $variant)
                    <label class="cursor-pointer">
                        <input type="radio"
                               wire:model.live="variantId"
                               value="{{ $variant->id }}"
                               class="sr-only peer">
                        <span class="inline-block border border-[#E5E5E5] text-xs px-4 py-2 peer-checked:border-[#1a1a1a] peer-checked:bg-[#1a1a1a] peer-checked:text-white transition-all cursor-pointer hover:border-[#1a1a1a]">
                            {{ $variant->name }} — {{ number_format($variant->price, 0, ',', '.') }}₫
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Quantity + Stock --}}
    <div class="flex items-center gap-6 mb-6">
        <div class="flex items-center border border-[#E5E5E5]" role="group" aria-label="Số lượng">
            <button
                type="button"
                wire:click="decrement"
                aria-label="Giảm số lượng"
                class="w-10 h-10 flex items-center justify-center text-[#888888] hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                </svg>
            </button>
            <span class="w-10 text-center text-sm border-l border-r border-[#E5E5E5] py-2 select-none"
                  aria-live="polite"
                  aria-label="Số lượng: {{ $quantity }}">
                {{ $quantity }}
            </span>
            <button
                type="button"
                wire:click="increment"
                aria-label="Tăng số lượng"
                class="w-10 h-10 flex items-center justify-center text-[#888888] hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>

        <span class="text-xs {{ $product->stock > 0 ? 'text-green-700' : 'text-[#E84444]' }}"
              aria-live="polite">
            {{ $product->stock > 0 ? 'Còn ' . $product->stock . ' sản phẩm' : 'Hết hàng' }}
        </span>
    </div>

    {{-- Add to cart button --}}
    <button
        type="button"
        wire:click="addToCart"
        @if($product->stock <= 0) disabled @endif
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50"
        class="btn-dark w-full disabled:opacity-40 disabled:cursor-not-allowed transition-opacity"
    >
        <span wire:loading.remove wire:target="addToCart">Thêm Vào Giỏ Hàng</span>
        <span wire:loading wire:target="addToCart" class="flex items-center justify-center gap-3">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Đang thêm...
        </span>
    </button>
</div>
