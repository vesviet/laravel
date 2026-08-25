<div class="space-y-6">
    {{-- Variant selector (if variants exist) --}}
    @if($product->variants->count() > 0)
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-primary-dark">Tùy chọn phiên bản:</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($product->variants as $variant)
                    <label class="cursor-pointer">
                        <input type="radio"
                               wire:model.live="variantId"
                               value="{{ $variant->id }}"
                               class="sr-only peer">
                        <span class="inline-block border text-xs px-3.5 py-2 transition-all cursor-pointer select-none
                                     peer-checked:border-badge-hot peer-checked:text-badge-hot peer-checked:bg-[#FFF5F5] peer-checked:font-medium
                                     border-border-subtle text-primary-dark bg-white hover:border-primary-dark">
                            {{ $variant->name }} — {{ number_format($variant->price, 0, ',', '.') }}₫
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Số lượng (Quantity Stepper matching user screenshot) --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-primary-dark">Số lượng</span>
            <span class="text-xs {{ $product->stock > 0 ? 'text-emerald-600' : 'text-badge-hot' }}">
                {{ $product->stock > 0 ? 'Còn hàng (' . $product->stock . ' sp)' : 'Hết hàng' }}
            </span>
        </div>

        <div class="inline-flex items-center border border-[#D1D5DB] bg-white rounded-none select-none" role="group" aria-label="Số lượng">
            <button
                type="button"
                wire:click="decrement"
                aria-label="Giảm số lượng"
                class="w-10 h-10 flex items-center justify-center text-[#4B5563] hover:text-[#111827] hover:bg-gray-50 transition-colors focus:outline-none"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                </svg>
            </button>
            <span class="w-12 text-center text-sm font-medium text-[#111827] border-l border-r border-[#D1D5DB] py-2"
                  aria-live="polite">
                {{ $quantity }}
            </span>
            <button
                type="button"
                wire:click="increment"
                aria-label="Tăng số lượng"
                class="w-10 h-10 flex items-center justify-center text-[#4B5563] hover:text-[#111827] hover:bg-gray-50 transition-colors focus:outline-none"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Actions Row: Wishlist + Bag Icon + Red Prominent CTA (Thêm vào giỏ hàng) --}}
    <div class="flex items-center gap-3 pt-2">
        {{-- Wishlist Button --}}
        @auth('customer')
            <div class="shrink-0">
                <livewire:wishlist-button :product="$product" :key="'add-wb-'.$product->id" />
            </div>
        @else
            <a href="{{ route('account.login') }}"
               class="w-12 h-12 flex items-center justify-center border border-[#D1D5DB] bg-white text-[#9CA3AF] hover:text-badge-hot hover:border-badge-hot transition-colors shrink-0"
               aria-label="Thêm vào danh sách yêu thích"
               title="Yêu thích">
                <x-icons.heart stroke="1.75" />
            </a>
        @endauth

        {{-- Share / Copy Link Icon Button --}}
        <button
            type="button"
            onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết sản phẩm!');"
            class="w-12 h-12 flex items-center justify-center border border-[#D1D5DB] bg-white text-[#6B7280] hover:text-[#111827] hover:border-[#111827] transition-colors shrink-0"
            aria-label="Chia sẻ sản phẩm"
            title="Sao chép liên kết">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
            </svg>
        </button>

        {{-- Primary CTA Button: Red "Thêm vào giỏ hàng" --}}
        <button
            type="button"
            wire:click="addToCart"
            @if($product->stock <= 0) disabled @endif
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75"
            class="flex-1 h-12 flex items-center justify-center font-medium text-sm text-white bg-badge-hot hover:bg-[#D32F2F] active:bg-[#B71C1C] transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="addToCart" class="tracking-wide">Thêm vào giỏ hàng</span>
            <span wire:loading wire:target="addToCart" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Đang xử lý...</span>
            </span>
        </button>
    </div>
</div>
