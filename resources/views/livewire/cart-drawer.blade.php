<div>
    {{-- ══════════════════════════════════════
         BACKDROP
         ══════════════════════════════════════ --}}
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-transition:enter="transition ease-in-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/30 z-[80]"
        style="display: none;"
        aria-hidden="true"
        @click="open = false"
    ></div>

    {{-- ══════════════════════════════════════
         DRAWER PANEL — 420px, slides from right
         ══════════════════════════════════════ --}}
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-400"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[90] flex pointer-events-none"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="Giỏ hàng"
    >
        <div class="w-screen max-w-[420px] pointer-events-auto flex flex-col bg-white h-full">

            {{-- ── Header ── --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-[#E5E5E5]">
                <h2 class="text-sm font-medium tracking-[0.15em] uppercase" id="cart-drawer-title">
                    Giỏ Hàng
                    @if(!empty($cartItems))
                        <span class="ml-1 text-[#888888] font-light">({{ count($cartItems) }})</span>
                    @endif
                </h2>
                <button
                    type="button"
                    wire:click="closeCart"
                    class="w-8 h-8 flex items-center justify-center text-[#888888] hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                    aria-label="Đóng giỏ hàng"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ── Cart Items (scrollable) ── --}}
            <div class="flex-1 overflow-y-auto px-6 py-2">
                @if(!empty($cartItems))
                    <ul role="list" aria-live="polite" aria-label="Sản phẩm trong giỏ hàng"
                        class="divide-y divide-[#E5E5E5]">
                        @foreach($cartItems as $item)
                            <li class="py-5 flex items-start gap-4">

                                {{-- Thumbnail --}}
                                <div class="w-[72px] h-[72px] flex-shrink-0 bg-[#E8E4DF] overflow-hidden flex items-center justify-center">
                                    @if($item['image_path'] ?? null)
                                        <img src="{{ $item['image_path'] }}"
                                             alt="{{ $item['product_name'] }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy">
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0 flex flex-col gap-1">
                                    <a href="{{ route('products.show', $item['slug'] ?? '#') }}"
                                       class="text-sm leading-snug hover:opacity-60 transition-opacity line-clamp-2">
                                        {{ $item['product_name'] }}
                                    </a>
                                    @if($item['variant_name'])
                                        <p class="text-xs text-[#888888]">{{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="text-sm font-medium">{{ number_format($item['price'], 0, ',', '.') }}₫</p>

                                    {{-- Qty controls --}}
                                    <div class="flex items-center gap-3 mt-1">
                                        <div class="flex items-center border border-[#E5E5E5]">
                                            <button
                                                type="button"
                                                wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }}, {{ max(1, $item['quantity'] - 1) }})"
                                                class="w-7 h-7 flex items-center justify-center text-[#888888] hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                                                aria-label="Giảm số lượng {{ $item['product_name'] }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="w-7 text-center text-sm" aria-live="polite" aria-label="Số lượng: {{ $item['quantity'] }}">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }}, {{ $item['quantity'] + 1 }})"
                                                class="w-7 h-7 flex items-center justify-center text-[#888888] hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                                                aria-label="Tăng số lượng {{ $item['product_name'] }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }})"
                                            class="text-[10px] tracking-[0.12em] uppercase text-[#888888] hover:text-[#E84444] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                                            aria-label="Xoá {{ $item['product_name'] }} khỏi giỏ hàng"
                                        >
                                            Xoá
                                        </button>
                                    </div>
                                </div>

                                {{-- Line total --}}
                                <p class="text-sm font-medium shrink-0">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    {{-- Empty state --}}
                    <div class="flex flex-col items-center justify-center h-full py-20 text-center" role="status">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#d0c8c0] mb-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                        </svg>
                        <p class="text-sm text-[#888888] mb-6">Giỏ hàng của bạn đang trống</p>
                        <button
                            type="button"
                            wire:click="closeCart"
                            class="link-underline text-[#1a1a1a]"
                        >
                            Tiếp tục mua sắm
                        </button>
                    </div>
                @endif
            </div>

            {{-- ── Footer: subtotal + checkout ── --}}
            @if(!empty($cartItems))
                <div class="border-t border-[#E5E5E5] px-6 py-6 bg-white">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-[10px] tracking-[0.15em] uppercase text-[#888888]">Tạm tính</span>
                        <span class="text-lg font-medium" wire:loading.class="opacity-50">
                            {{ number_format($this->subtotal, 0, ',', '.') }}₫
                        </span>
                    </div>
                    <p class="text-xs text-[#888888] mb-6 font-light">Phí vận chuyển sẽ được tính khi thanh toán</p>

                    <a href="{{ route('checkout.index') }}"
                       class="btn-dark block w-full text-center mb-3">
                        Tiến Hành Thanh Toán
                    </a>

                    <button
                        type="button"
                        wire:click="closeCart"
                        class="w-full text-[10px] tracking-[0.15em] uppercase text-[#888888] py-2 hover:text-[#1a1a1a] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
                    >
                        Tiếp tục mua sắm
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
