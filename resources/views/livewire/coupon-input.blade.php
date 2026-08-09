<div>
    {{-- Coupon applied state --}}
    @if($couponApplied)
        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800">Mã <span class="font-bold">{{ $couponApplied }}</span> đã áp dụng!</p>
                    <p class="text-xs text-green-700">Giảm: <span class="font-semibold">-{{ number_format($discount, 0, ',', '.') }}₫</span></p>
                </div>
            </div>
            <button
                type="button"
                wire:click="removeCoupon"
                class="text-green-600 hover:text-green-800 text-sm font-medium ml-4 flex-shrink-0"
            >
                Xóa
            </button>
        </div>
    @else
        {{-- Input form --}}
        <div class="flex gap-2">
            <input
                type="text"
                wire:model="couponCode"
                placeholder="Nhập mã giảm giá"
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm uppercase"
            >
            <button
                type="button"
                wire:click="applyCoupon"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex-shrink-0"
            >
                <span wire:loading.remove wire:target="applyCoupon">Áp dụng</span>
                <span wire:loading wire:target="applyCoupon" class="flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Đang kiểm tra...
                </span>
            </button>
        </div>

        {{-- Error message --}}
        @if($errorMessage)
            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $errorMessage }}
            </p>
        @endif
    @endif
</div>
