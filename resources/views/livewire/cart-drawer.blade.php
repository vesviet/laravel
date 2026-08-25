<div>
    {{-- ══════════════════════════════════════
         BACKDROP
         ══════════════════════════════════════ --}}
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-[80]"
        style="display: none;"
        aria-hidden="true"
        @click="open = false"
    ></div>

    {{-- ══════════════════════════════════════
         DRAWER PANEL — 440px, slides from right
         ══════════════════════════════════════ --}}
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.window.escape="open = false"
        class="fixed inset-y-0 right-0 z-[90] flex pointer-events-none"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        aria-label="Giỏ hàng mua sắm"
    >
        <div class="w-screen max-w-[440px] pointer-events-auto flex flex-col bg-white h-full shadow-2xl">

            {{-- ── Header ── --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-border-subtle bg-white shrink-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-semibold tracking-[0.15em] uppercase text-primary-dark" id="cart-drawer-title">
                        Giỏ Hàng
                    </h2>
                    @if(!empty($cartItems))
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-surface-bg text-primary-dark">
                            {{ $this->totalQuantity }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if(!empty($cartItems))
                        <button
                            type="button"
                            wire:click="clearCart"
                            wire:confirm="Bạn có chắc chắn muốn làm trống giỏ hàng?"
                            class="text-[10px] tracking-[0.1em] uppercase text-muted-text hover:text-badge-hot transition-colors px-2 py-1"
                            aria-label="Làm trống giỏ hàng"
                        >
                            Xoá hết
                        </button>
                    @endif

                    <button
                        type="button"
                        wire:click="closeCart"
                        class="w-8 h-8 flex items-center justify-center text-muted-text hover:text-primary-dark transition-colors rounded-full hover:bg-surface-bg focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-dark"
                        aria-label="Đóng giỏ hàng"
                    >
                        <x-icons.close class="w-5 h-5" />
                    </button>
                </div>
            </div>

            {{-- ── Smart Nudge Progress Bar (Prominently at Top) ── --}}
            @if(!empty($cartItems) && $this->smartNudge)
                @php $nudge = $this->smartNudge; @endphp
                <div class="px-6 py-3.5 border-b border-border-subtle bg-[#FBF9F6] shrink-0">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-1.5 text-xs font-medium text-primary-dark">
                            @if($nudge['icon'] === 'truck')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary-dark shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.125C14.25 3.504 13.746 3 13.125 3H3.375C2.754 3 2.25 3.504 2.25 4.125v10.125c0 .621.504 1.125 1.125 1.125h1.5" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#D97706] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                </svg>
                            @endif
                            <span class="leading-tight">{{ $nudge['message'] }}</span>
                        </div>
                        <span class="text-[10px] font-bold tracking-wider uppercase shrink-0 {{ $nudge['is_completed'] ? 'text-emerald-700' : 'text-muted-text' }}">
                            {{ $nudge['badge'] }}
                        </span>
                    </div>

                    {{-- Animated Progress Track --}}
                    <div class="w-full bg-border-subtle h-2 rounded-full overflow-hidden relative shadow-inner">
                        <div
                            class="h-full rounded-full transition-all duration-700 ease-out {{ $nudge['is_completed'] ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-primary-dark via-[#4A4A5A] to-primary-dark' }}"
                            style="width: {{ $nudge['progress_percent'] }}%;"
                        ></div>
                    </div>
                </div>
            @endif

            {{-- ── Cart Items (Scrollable Body) ── --}}
            <div class="flex-1 overflow-y-auto px-6 py-2 divide-y divide-border-subtle">
                @if(!empty($cartItems))
                    <ul role="list" aria-live="polite" aria-label="Danh sách sản phẩm trong giỏ hàng" class="divide-y divide-border-subtle">
                        @foreach($cartItems as $item)
                            <li class="py-4 flex items-start gap-4" wire:key="cart-item-{{ $item['product_id'] }}-{{ $item['product_variant_id'] ?? 0 }}">

                                {{-- Product Thumbnail --}}
                                <div class="w-16 h-16 flex-shrink-0 bg-[#E8E4DF] rounded-sm overflow-hidden flex items-center justify-center relative border border-border-subtle">
                                    @if(!empty($item['image_path']))
                                        <img src="{{ $item['image_path'] }}"
                                             alt="{{ $item['product_name'] }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy">
                                    @else
                                        <x-icons.image-placeholder class="w-6 h-6 text-muted-text" />
                                    @endif

                                    @if(!empty($item['is_flash_sale']))
                                        <span class="absolute top-0 left-0 bg-badge-hot text-white text-[8px] font-bold px-1 py-0.2 uppercase tracking-tight">
                                            FLASH SALE
                                        </span>
                                    @endif
                                </div>

                                {{-- Info & Controls --}}
                                <div class="flex-1 min-w-0 flex flex-col gap-1">
                                    <a href="{{ route('products.show', $item['slug'] ?? '#') }}"
                                       class="text-sm font-medium text-primary-dark hover:text-muted-text transition-colors line-clamp-1 leading-snug">
                                        {{ $item['product_name'] }}
                                    </a>

                                    @if(!empty($item['variant_name']))
                                        <p class="text-xs text-muted-text font-light">Phân loại: {{ $item['variant_name'] }}</p>
                                    @endif

                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xs font-medium text-primary-dark">
                                            {{ number_format($item['price'], 0, ',', '.') }}₫
                                        </span>
                                    </div>

                                    {{-- Quantity buttons & Delete action --}}
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <div class="flex items-center border border-border-subtle rounded-sm bg-white">
                                            <button
                                                type="button"
                                                wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }}, {{ max(1, $item['quantity'] - 1) }})"
                                                wire:loading.attr="disabled"
                                                class="w-6 h-6 flex items-center justify-center text-muted-text hover:text-primary-dark transition-colors focus:outline-none disabled:opacity-50"
                                                aria-label="Giảm số lượng"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="w-7 text-center text-xs font-medium text-primary-dark" aria-live="polite">
                                                {{ $item['quantity'] }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="updateQuantity({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }}, {{ $item['quantity'] + 1 }})"
                                                wire:loading.attr="disabled"
                                                class="w-6 h-6 flex items-center justify-center text-muted-text hover:text-primary-dark transition-colors focus:outline-none disabled:opacity-50"
                                                aria-label="Tăng số lượng"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $item['product_id'] }}, {{ $item['product_variant_id'] ?? 'null' }})"
                                            wire:loading.attr="disabled"
                                            class="text-[10px] tracking-[0.1em] uppercase text-muted-text hover:text-badge-hot transition-colors focus:outline-none"
                                            aria-label="Xoá sản phẩm khỏi giỏ"
                                        >
                                            Xoá
                                        </button>
                                    </div>
                                </div>

                                {{-- Line Total --}}
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-semibold text-primary-dark">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    {{-- Empty Cart State --}}
                    <div class="flex flex-col items-center justify-center h-full py-20 text-center" role="status">
                        <div class="w-16 h-16 rounded-full bg-surface-bg flex items-center justify-center mb-4 text-muted-text">
                            <x-icons.shopping-bag class="w-8 h-8" stroke="1.25" />
                        </div>
                        <p class="text-sm font-medium text-primary-dark mb-1">Giỏ hàng của bạn đang trống</p>
                        <p class="text-xs text-muted-text mb-6 font-light">Hãy khám phá các sản phẩm nội thất tinh tế của chúng tôi</p>
                        <button
                            type="button"
                            wire:click="closeCart"
                            class="btn-dark !py-2.5 !px-6 text-xs uppercase tracking-widest"
                        >
                            Tiếp tục mua sắm
                        </button>
                    </div>
                @endif
            </div>

            {{-- ── 1-Click Available Coupons Tray (Interactive Collapsible) ── --}}
            @if(!empty($cartItems))
                <div class="border-t border-border-subtle bg-[#FAF8F5] px-6 py-3 shrink-0">
                    <div class="flex items-center justify-between">
                        <button
                            type="button"
                            wire:click="toggleCouponsTray"
                            class="flex items-center gap-2 text-xs font-semibold tracking-wider uppercase text-primary-dark hover:opacity-80 transition-opacity"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                            </svg>
                            <span>Mã Ưu Đãi & Voucher</span>
                            @if($this->availableCoupons->isNotEmpty())
                                <span class="bg-primary-dark text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">
                                    {{ $this->availableCoupons->count() }}
                                </span>
                            @endif
                        </button>

                        <button
                            type="button"
                            wire:click="toggleCouponsTray"
                            class="text-xs text-muted-text hover:text-primary-dark flex items-center gap-1 transition-transform duration-200"
                        >
                            <span class="text-[10px] uppercase tracking-widest">{{ $isCouponsTrayOpen ? 'Thu gọn' : 'Xem mã' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transform transition-transform {{ $isCouponsTrayOpen ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>

                    {{-- Manual Input Row --}}
                    <div class="mt-2.5 flex items-center gap-2">
                        <input
                            type="text"
                            wire:model.defer="couponCode"
                            placeholder="Nhập mã ưu đãi..."
                            aria-label="Mã ưu đãi"
                            class="flex-1 text-xs border border-border-subtle px-3 py-2 uppercase tracking-wider rounded-sm bg-white text-primary-dark placeholder-muted-text focus:outline-none focus:border-primary-dark"
                        />
                        <button
                            type="button"
                            wire:click="applyCoupon()"
                            wire:loading.attr="disabled"
                            class="btn-dark !py-2 !px-4 text-[10px] font-semibold tracking-widest uppercase shrink-0"
                        >
                            <span wire:loading.remove wire:target="applyCoupon">Áp dụng</span>
                            <span wire:loading wire:target="applyCoupon">...</span>
                        </button>
                    </div>

                    {{-- Alert Messages --}}
                    @if($couponError)
                        <p class="text-[11px] text-badge-hot mt-1.5 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $couponError }}</span>
                        </p>
                    @endif

                    @if($couponSuccess)
                        <p class="text-[11px] text-emerald-700 mt-1.5 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $couponSuccess }}</span>
                        </p>
                    @endif

                    {{-- Collapsible Available Coupons List --}}
                    @if($isCouponsTrayOpen && $this->availableCoupons->isNotEmpty())
                        <div class="mt-3 space-y-2 max-h-48 overflow-y-auto pr-1">
                            @foreach($this->availableCoupons as $coupon)
                                <div class="p-2.5 bg-white border border-border-subtle rounded-sm flex items-center justify-between gap-2 shadow-2xs hover:border-primary-dark transition-colors"
                                     wire:key="available-coupon-{{ $coupon->id }}">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-[11px] font-bold px-1.5 py-0.5 bg-surface-bg text-primary-dark border border-dashed border-muted-text rounded">
                                                {{ $coupon->code }}
                                            </span>
                                            <span class="text-xs font-semibold text-primary-dark">
                                                {{ $coupon->formatted_discount }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-muted-text mt-0.5 line-clamp-1">
                                            {{ $coupon->name }} — {{ $coupon->min_order_formatted }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 flex items-center">
                                        @if($coupon->is_applied)
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-300 px-2 py-1 rounded">
                                                    ✓ Đang dùng
                                                </span>
                                                <button
                                                    type="button"
                                                    wire:click="removeCoupon"
                                                    class="text-[10px] text-muted-text hover:text-badge-hot underline"
                                                    title="Gỡ mã"
                                                >
                                                    Gỡ
                                                </button>
                                            </div>
                                        @elseif($coupon->is_eligible)
                                            <button
                                                type="button"
                                                wire:click="applyCoupon('{{ $coupon->code }}')"
                                                wire:loading.attr="disabled"
                                                class="btn-dark !py-1 !px-3 text-[10px] font-medium uppercase tracking-wider"
                                            >
                                                Áp dụng
                                            </button>
                                        @else
                                            <span class="text-[10px] text-muted-text bg-surface-bg px-2 py-1 rounded cursor-not-allowed" title="{{ $coupon->ineligible_reason }}">
                                                {{ $coupon->ineligible_reason }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── Footer: Financial Breakdown & Checkout CTA ── --}}
            @if(!empty($cartItems))
                <div class="border-t border-border-subtle px-6 py-5 bg-white shrink-0">
                    <div class="space-y-2 mb-4 text-sm">

                        {{-- Subtotal (Tạm tính) --}}
                        <div class="flex justify-between items-baseline text-muted-text">
                            <span class="text-xs font-light">Tạm tính</span>
                            <span class="text-sm font-medium text-primary-dark">
                                {{ number_format($this->subtotal, 0, ',', '.') }}₫
                            </span>
                        </div>

                        {{-- Item/Cart Automatic Rule Discounts --}}
                        @if($this->breakdown->itemDiscounts > 0)
                            <div class="flex justify-between items-baseline text-emerald-700 text-xs">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                    </svg>
                                    <span>Khuyến mãi đơn hàng</span>
                                </span>
                                <span class="font-semibold">
                                    -{{ number_format($this->breakdown->itemDiscounts, 0, ',', '.') }}₫
                                </span>
                            </div>
                        @endif

                        {{-- Applied Coupon Code Line Item --}}
                        @if($this->appliedCouponCode && $this->breakdown->couponDiscount > 0)
                            <div class="flex justify-between items-center text-emerald-700 text-xs bg-emerald-50 px-2 py-1.5 rounded border border-emerald-200">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-bold uppercase tracking-wider text-[11px] bg-white px-1.5 py-0.5 rounded border border-emerald-300">
                                        {{ $this->appliedCouponCode }}
                                    </span>
                                    <span class="text-[11px]">Mã giảm giá</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">
                                        -{{ number_format($this->breakdown->couponDiscount, 0, ',', '.') }}₫
                                    </span>
                                    <button
                                        type="button"
                                        wire:click="removeCoupon"
                                        class="text-muted-text hover:text-badge-hot transition-colors focus:outline-none"
                                        title="Gỡ mã giảm giá"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Shipping Discount Status --}}
                        <div class="flex justify-between items-baseline text-xs text-muted-text">
                            <span>Vận chuyển</span>
                            @if($this->breakdown->shippingDiscount > 0)
                                <div class="flex items-center gap-1.5">
                                    <span class="line-through text-muted-text">{{ number_format($this->estimatedShippingFee, 0, ',', '.') }}₫</span>
                                    <span class="text-emerald-700 font-semibold uppercase text-[10px] bg-emerald-50 border border-emerald-300 px-1.5 py-0.2 rounded">
                                        Miễn phí
                                    </span>
                                </div>
                            @else
                                <span class="font-light">Tính khi thanh toán</span>
                            @endif
                        </div>

                        {{-- Free Gifts (BXGY) --}}
                        @if(!empty($this->breakdown->freeGifts))
                            @foreach($this->breakdown->freeGifts as $gift)
                                <div class="flex justify-between items-center text-xs text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200">
                                    <span class="flex items-center gap-1">
                                        <span>🎁 Quà tặng:</span>
                                        <strong class="font-medium">{{ $gift['name'] ?? 'Sản phẩm quà tặng' }}</strong>
                                    </span>
                                    <span class="text-[10px] font-bold uppercase bg-amber-200 px-1 py-0.2 rounded">FREE</span>
                                </div>
                            @endforeach
                        @endif

                        {{-- Total Net (Tổng thanh toán) --}}
                        <div class="border-t border-border-subtle pt-3 flex justify-between items-baseline">
                            <span class="text-xs font-semibold tracking-[0.15em] uppercase text-primary-dark">
                                Tổng Thanh Toán
                            </span>
                            <div class="text-right">
                                <span class="text-lg font-bold text-primary-dark" wire:loading.class="opacity-50">
                                    {{ number_format($this->netTotal, 0, ',', '.') }}₫
                                </span>
                                @if($this->totalDiscount > 0)
                                    <p class="text-[11px] text-emerald-700 font-medium mt-0.5">
                                        Tiết kiệm được {{ number_format($this->totalDiscount, 0, ',', '.') }}₫
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Primary Checkout Action --}}
                    <a href="{{ route('checkout.index') }}"
                       class="btn-dark block w-full text-center py-3 text-xs tracking-widest uppercase mb-2 shadow-sm hover:shadow-md transition-shadow">
                        Tiến Hành Thanh Toán
                    </a>

                    <button
                        type="button"
                        wire:click="closeCart"
                        class="w-full text-[10px] tracking-[0.15em] uppercase text-muted-text py-1.5 hover:text-primary-dark transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-dark"
                    >
                        Tiếp tục mua sắm
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
