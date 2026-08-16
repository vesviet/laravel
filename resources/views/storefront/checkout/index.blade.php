@extends('layouts.storefront')

@pushonce('page_title')Thanh Toán @endpushonce
@pushonce('meta_description')Hoàn tất đơn hàng của bạn tại MYSHOP.@endpushonce

@section('content')

<div class="py-10 md:py-16">
    <div class="section-wrapper">

        {{-- Page heading --}}
        <h1 class="text-2xl font-medium tracking-wide mb-10">Thanh Toán</h1>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 text-sm mb-8" role="alert" aria-live="assertive">
                {{ session('error') }}
            </div>
        @endif

        {{-- ── 60/40 two-column grid ── --}}
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

            {{-- ══════════════════════════
                 LEFT (60%): Form
                 ══════════════════════════ --}}
            <div class="w-full lg:w-[60%]">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    {{-- ── Section: Shipping info ── --}}
                    <div class="mb-10">
                        <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                            Thông Tin Giao Hàng
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                            {{-- Name --}}
                            <div class="md:col-span-1">
                                <label for="customer_name" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Họ và tên <span class="text-[#E84444]">*</span>
                                </label>
                                <input type="text"
                                       name="customer_name"
                                       id="customer_name"
                                       value="{{ old('customer_name', $customer?->name) }}"
                                       required
                                       autocomplete="name"
                                       class="input-underline w-full @error('customer_name') border-[#E84444] @enderror"
                                       aria-required="true"
                                       aria-describedby="@error('customer_name') error-customer_name @enderror">
                                @error('customer_name')
                                    <span id="error-customer_name" class="text-[#E84444] text-xs mt-1 block" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="md:col-span-1">
                                <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Số điện thoại <span class="text-[#E84444]">*</span>
                                </label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $customer?->phone) }}"
                                       required
                                       autocomplete="tel"
                                       class="input-underline w-full @error('phone') border-[#E84444] @enderror"
                                       aria-required="true"
                                       aria-describedby="@error('phone') error-phone @enderror">
                                @error('phone')
                                    <span id="error-phone" class="text-[#E84444] text-xs mt-1 block" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Email <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $customer?->email) }}"
                                       autocomplete="email"
                                       class="input-underline w-full @error('email') border-[#E84444] @enderror"
                                       aria-describedby="@error('email') error-email @enderror">
                                @error('email')
                                    <span id="error-email" class="text-[#E84444] text-xs mt-1 block" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <label for="address" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Địa chỉ <span class="text-[#E84444]">*</span>
                                </label>
                                <input type="text"
                                       name="address"
                                       id="address"
                                       value="{{ old('address') }}"
                                       required
                                       autocomplete="street-address"
                                       class="input-underline w-full @error('address') border-[#E84444] @enderror"
                                       aria-required="true"
                                       aria-describedby="@error('address') error-address @enderror">
                                @error('address')
                                    <span id="error-address" class="text-[#E84444] text-xs mt-1 block" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Province --}}
                            <div>
                                <label for="city" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Tỉnh / Thành phố
                                </label>
                                <select name="city"
                                        id="city"
                                        autocomplete="address-level1"
                                        class="input-underline w-full cursor-pointer">
                                    <option value="">-- Chọn tỉnh/thành --</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->name }}"
                                                {{ old('city') === $province->name ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- District --}}
                            <div>
                                <label for="district" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Quận / Huyện
                                </label>
                                <input type="text"
                                       name="district"
                                       id="district"
                                       value="{{ old('district') }}"
                                       autocomplete="address-level2"
                                       class="input-underline w-full">
                            </div>

                            {{-- Ward --}}
                            <div>
                                <label for="ward" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Phường / Xã
                                </label>
                                <input type="text"
                                       name="ward"
                                       id="ward"
                                       value="{{ old('ward') }}"
                                       class="input-underline w-full">
                            </div>

                            {{-- Notes --}}
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                    Ghi chú đơn hàng <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span>
                                </label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="2"
                                          class="input-underline w-full resize-none">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ── Section: Payment method ── --}}
                    <div class="mb-10">
                        <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                            Phương Thức Thanh Toán
                        </h2>
                        <div class="flex items-start gap-3 py-4 border border-[#E5E5E5] px-4">
                            <input id="payment_cod"
                                   name="payment_method"
                                   type="radio"
                                   value="cod"
                                   checked
                                   class="mt-0.5 h-4 w-4 accent-[#1a1a1a] cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]">
                            <div>
                                <label for="payment_cod" class="text-sm font-medium cursor-pointer">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                                <p class="text-xs text-[#888888] mt-1 font-light">Bạn thanh toán bằng tiền mặt khi nhận hàng.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Section: Coupon ── --}}
                    <div class="mb-10">
                        <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                            Mã Giảm Giá
                        </h2>
                        @livewire('coupon-input', ['subtotal' => $subtotal])
                    </div>

                    {{-- ── Submit ── --}}
                    <div x-data="{ submitting: false }">
                        <button
                            type="submit"
                            id="place-order-btn"
                            @click="submitting = true"
                            :disabled="submitting"
                            class="btn-dark w-full transition-opacity"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                        >
                            <span x-show="!submitting">Đặt Hàng</span>
                            <span x-show="submitting" class="flex items-center justify-center gap-3" style="display: none;">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Đang xử lý...
                            </span>
                        </button>
                    </div>

                </form>
            </div>

            {{-- ══════════════════════════
                 RIGHT (40%): Order summary
                 ══════════════════════════ --}}
            <div class="w-full lg:w-[40%]">
                <div class="sticky top-24 bg-white">

                    <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                        Tóm Tắt Đơn Hàng
                    </h2>

                    {{-- Cart items --}}
                    <ul class="divide-y divide-[#E5E5E5] mb-6" aria-label="Sản phẩm trong đơn hàng">
                        @foreach($cart as $item)
                            <li class="py-4 flex items-start gap-4">
                                {{-- Thumbnail --}}
                                @if($item['image_path'] ?? null)
                                    <div class="w-16 h-16 flex-shrink-0 bg-[#F0F0F0] overflow-hidden">
                                        <img src="{{ Storage::url($item['image_path']) }}"
                                             alt="{{ $item['product_name'] }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div class="w-16 h-16 flex-shrink-0 bg-[#E8E4DF]"></div>
                                @endif

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm leading-snug truncate">{{ $item['product_name'] }}</p>
                                    @if($item['variant_name'])
                                        <p class="text-xs text-[#888888] mt-0.5">{{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="text-xs text-[#888888] mt-0.5">Số lượng: {{ $item['quantity'] }}</p>
                                </div>

                                {{-- Price --}}
                                <p class="text-sm font-medium shrink-0">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Totals --}}
                    <div id="order-totals" class="border-t border-[#E5E5E5] pt-5 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#888888]">Tạm tính</span>
                            <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#888888]">Phí vận chuyển</span>
                            <span class="text-green-700">Miễn phí</span>
                        </div>
                        @if($appliedCoupon)
                            <div class="flex justify-between text-sm text-green-700" id="discount-row">
                                <span>Giảm giá</span>
                                <span id="discount-amount">Đang tính...</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-4 border-t border-[#E5E5E5]">
                            <span class="text-sm font-medium tracking-wide">Tổng cộng</span>
                            <span class="text-lg font-medium" id="total-display">
                                {{ number_format($subtotal, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>

                    {{-- Trust signal --}}
                    <div class="mt-6 flex items-center gap-2 text-xs text-[#888888]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        Thanh toán an toàn. Thông tin được mã hoá SSL 256-bit.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Listen for Livewire coupon events and update the summary sidebar
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('coupon-applied', (event) => {
            const discount = event.discount;
            const subtotal = {{ $subtotal }};
            const newTotal = Math.max(0, subtotal - discount);

            // Show or update discount row
            let discountRow = document.getElementById('discount-row');
            if (!discountRow) {
                const totals = document.getElementById('order-totals');
                const totalRow = totals.querySelector('.border-t.pt-4');
                discountRow = document.createElement('div');
                discountRow.id = 'discount-row';
                discountRow.className = 'flex justify-between text-sm text-green-700';
                discountRow.innerHTML = `<span>Giảm giá</span><span id="discount-amount"></span>`;
                totals.insertBefore(discountRow, totalRow);
            }

            const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + '₫';
            document.getElementById('discount-amount').textContent = '-' + fmt(discount);
            document.getElementById('total-display').textContent = fmt(newTotal);
        });

        Livewire.on('coupon-removed', () => {
            const subtotal = {{ $subtotal }};
            const discountRow = document.getElementById('discount-row');
            if (discountRow) discountRow.remove();
            document.getElementById('total-display').textContent =
                new Intl.NumberFormat('vi-VN').format(subtotal) + '₫';
        });
    });
</script>
@endpush

@endsection
