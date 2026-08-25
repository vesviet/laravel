<div class="py-10 md:py-16">
    <div class="section-wrapper">

        {{-- Page heading --}}
        <h1 class="text-2xl font-medium tracking-wide mb-10">Thanh Toán</h1>

        {{-- Error message --}}
        @if($errorMessage)
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 text-sm mb-8" role="alert" aria-live="assertive">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Step indicator --}}
        <div class="mb-10">
            <div class="flex items-center justify-between">
                @foreach($steps as $index => $step)
                    @php
                        $isActive = $currentStep === $step;
                        $isCompleted = array_search($currentStep, $steps) > $index;
                        $stepLabels = [
                            "shipping" => "Thông tin giao hàng",
                            "payment" => "Phương thức thanh toán",
                            "review" => "Xác nhận đơn hàng",
                        ];
                    @endphp
                    <div class="flex flex-col items-center relative">
                        {{-- Line --}}
                        @if($index < count($steps) - 1)
                            <div class="absolute top-4 left-1/2 w-full h-1 bg-[#E5E5E5] z-0"
                                :class="{ 'bg-[#E84444]': @json($isCompleted) }"></div>
                        @endif

                        {{-- Circle --}}
                        <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center
                            {{ $isActive
                                ? "bg-[#E84444] text-white border-2 border-[#E84444]"
                                : ($isCompleted ? "bg-[#E84444] text-white" : "bg-white text-[#888888] border-2 border-[#E5E5E5]") }}"
                        >
                            @if($isCompleted)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            @elseif($isActive)
                                <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            @else
                                <span class="text-sm font-medium">{{ $index + 1 }}</span>
                            @endif
                        </div>

                        {{-- Label --}}
                        <span class="mt-2 text-[10px] uppercase tracking-wider font-medium
                            {{ $isActive ? "text-[#E84444]" : ($isCompleted ? "text-[#E84444]" : "text-[#888888]") }}">
                            {{ $stepLabels[$step] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main content --}}
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

            {{-- LEFT (60%): Form Steps --}}
            <div class="w-full lg:w-[60%]">
                {{-- Shipping Step --}}
                <div x-show="currentStep === 'shipping'" x-transition>
                    <div wire:key="shipping-step">
                        <form wire:submit.prevent="nextStep" class="space-y-8" @submit.prevent="return false">

                            {{-- Section: Shipping info --}}
                            <div>
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
                                               wire:model="shippingData.customer_name"
                                               id="customer_name"
                                               required
                                               autocomplete="name"
                                               class="input-underline w-full @error('shippingData.customer_name') border-[#E84444] @enderror"
                                               aria-required="true">
                                        @error("shippingData.customer_name")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Phone --}}
                                    <div class="md:col-span-1">
                                        <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Số điện thoại <span class="text-[#E84444]">*</span>
                                        </label>
                                        <input type="tel"
                                               wire:model="shippingData.phone"
                                               id="phone"
                                               required
                                               autocomplete="tel"
                                               class="input-underline w-full @error('shippingData.phone') border-[#E84444] @enderror"
                                               aria-required="true">
                                        @error("shippingData.phone")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="md:col-span-2">
                                        <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Email <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span>
                                        </label>
                                        <input type="email"
                                               wire:model="shippingData.email"
                                               id="email"
                                               autocomplete="email"
                                               class="input-underline w-full @error('shippingData.email') border-[#E84444] @enderror">
                                        @error("shippingData.email")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Address Selector (authenticated only) --}}
                                    @if(auth("customer")->check())
                                        <div class="md:col-span-2">
                                            <livewire:address-selector
                                                :customer="$customer"
                                                address-type="shipping"
                                                :selected-address="selectedAddress"
                                                key="address-selector-shipping"
                                            />
                                        </div>
                                    @endif

                                    {{-- Manual address fields --}}
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Địa chỉ <span class="text-[#E84444]">*</span>
                                        </label>
                                        <input type="text"
                                               wire:model="shippingData.address"
                                               id="address"
                                               required
                                               autocomplete="street-address"
                                               class="input-underline w-full @error('shippingData.address') border-[#E84444] @enderror"
                                               aria-required="true">
                                        @error("shippingData.address")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Province --}}
                                    <div>
                                        <label for="city" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Tỉnh / Thành phố
                                        </label>
                                        <select wire:model="shippingData.city"
                                                id="city"
                                                autocomplete="address-level1"
                                                class="input-underline w-full cursor-pointer @error('shippingData.city') border-[#E84444] @enderror">
                                            <option value="">-- Chọn tỉnh/thành --</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->name }}"
                                                        {{ $shippingData["city"] === $province->name ? "selected" : "" }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("shippingData.city")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- District --}}
                                    <div>
                                        <label for="district" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Quận / Huyện
                                        </label>
                                        <input type="text"
                                               wire:model="shippingData.district"
                                               id="district"
                                               autocomplete="address-level2"
                                               class="input-underline w-full @error('shippingData.district') border-[#E84444] @enderror">
                                        @error("shippingData.district")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Ward --}}
                                    <div>
                                        <label for="ward" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Phường / Xã
                                        </label>
                                        <input type="text"
                                               wire:model="shippingData.ward"
                                               id="ward"
                                               class="input-underline w-full @error('shippingData.ward') border-[#E84444] @enderror">
                                        @error("shippingData.ward")
                                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Notes --}}
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                                            Ghi chú đơn hàng <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span>
                                        </label>
                                        <textarea wire:model="shippingData.notes"
                                                  id="notes"
                                                  rows="2"
                                                  class="input-underline w-full resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Next button --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                        :disabled="isProcessing"
                                        class="btn-dark px-8 py-3 w-full sm:w-auto">
                                    <span x-show="!isProcessing">Tiếp theo: Thanh toán</span>
                                    <span x-show="isProcessing" class="flex items-center gap-3">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Đang xử lý...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Payment Step --}}
                <div x-show="currentStep === 'payment'" x-transition>
                    <div wire:key="payment-step">
                        <form wire:submit.prevent="nextStep" class="space-y-8" @submit.prevent="return false">

                            <div>
                                <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                                    Phương Thức Thanh Toán
                                </h2>

                                <livewire:payment-method-selector
                                    :available-methods="['cod', 'vnpay', 'momo', 'banking']"
                                    :selected-method="selectedPaymentMethod"
                                />

                                <div class="flex justify-end pt-4">
                                    <button type="button"
                                            wire:click="previousStep"
                                            class="mr-4 px-4 py-2 text-xs uppercase tracking-wider text-[#888888] hover:text-[#E84444] font-medium link-underline">
                                        Quay lại
                                    </button>
                                    <button type="submit"
                                            :disabled="isProcessing"
                                            class="btn-dark px-8 py-3">
                                        <span x-show="!isProcessing">Tiếp theo: Xác nhận</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Review Step --}}
                <div x-show="currentStep === 'review'" x-transition>
                    <div wire:key="review-step">
                        <form wire:submit.prevent="submitOrder" class="space-y-8" @submit.prevent="return false">

                            <div>
                                <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
                                    Xác Nhận Đơn Hàng
                                </h2>

                                <div class="space-y-6">
                                    {{-- Shipping Info Summary --}}
                                    <div class="bg-white border border-[#E5E5E5] p-4 rounded-lg">
                                        <h3 class="text-xs font-semibold tracking-[0.15em] uppercase text-[#23232C] mb-3">Thông tin giao hàng</h3>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <span class="text-[#888888]">Họ và tên</span>
                                                <p class="font-medium" x-text="shippingData.customer_name"></p>
                                            </div>
                                            <div>
                                                <span class="text-[#888888]">Điện thoại</span>
                                                <p class="font-medium" x-text="shippingData.phone"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[#888888]">Địa chỉ</span>
                                                <p class="font-medium" x-text="shippingData.address + ', ' + shippingData.ward + ', ' + shippingData.district + ', ' + shippingData.city"></p>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[#888888]">Ghi chú</span>
                                                <p class="font-medium" x-text="shippingData.notes || 'Không có'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Payment Method Summary --}}
                                    <div class="bg-white border border-[#E5E5E5] p-4 rounded-lg">
                                        <h3 class="text-xs font-semibold tracking-[0.15em] uppercase text-[#23232C] mb-3">Phương thức thanh toán</h3>
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-[#F0F0F0] border border-[#E5E5E5] rounded-lg flex items-center justify-center shrink-0">
                                                @if(selectedPaymentMethod === "cod")
                                                    <svg class="w-6 h-6 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @elseif(selectedPaymentMethod === "vnpay")
                                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                                @elseif(selectedPaymentMethod === "momo")
                                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17 12c0 2.76-2.24 5-5 5s-5-2.24-5-5 2.24-5 5-5 5 2.24 5 5zm-5 2c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/></svg>
                                                @elseif(selectedPaymentMethod === "banking")
                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium" x-text="getSelectedMethodDetails().name ?? selectedPaymentMethod"></p>
                                                <p class="text-xs text-[#888888]">{{ getSelectedMethodDetails().description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Place Order --}}
                                <div class="flex justify-end pt-4 border-t border-[#E5E5E5]">
                                    <button type="button"
                                            wire:click="previousStep"
                                            class="mr-4 px-4 py-2 text-xs uppercase tracking-wider text-[#888888] hover:text-[#E84444] font-medium link-underline">
                                        Quay lại
                                    </button>
                                    <button type="submit"
                                            :disabled="isProcessing"
                                            class="btn-dark px-8 py-3">
                                        <span x-show="!isProcessing">Đặt Hàng</span>
                                        <span x-show="isProcessing" class="flex items-center gap-3">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Đang xử lý...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            {{-- RIGHT (40%): Order Summary --}}
            <div class="w-full lg:w-[40%]">
                <div class="sticky top-24">
                    <livewire:order-summary
                        :breakdown="$breakdown"
                        :subtotal="$subtotal"
                        :shipping-fee="$shippingFee"
                        :show-free-gifts="true"
                        :show-promotions="true"
                        :show-shipping="true"
                        theme="light"
                    />

                    {{-- Coupon Input --}}
                    <div class="mt-6">
                        <livewire:coupon-input
                            :subtotal="$subtotal"
                            key="coupon-input-checkout"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Sync Alpine.js with Livewire
    document.addEventListener("livewire:initialized", () => {
        Livewire.on("currentStep", (step) => {
            window.currentStep = step;
        });
    });
</script>