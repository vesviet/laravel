<div>
    @if($couponApplied)
        {{-- Applied state --}}
        <div class="flex items-center justify-between border border-[#1a1a1a] px-4 py-3">
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm">
                        Mã <span class="font-medium">{{ $couponApplied }}</span> đã áp dụng
                    </p>
                    <p class="text-xs text-green-700 font-light">
                        Tiết kiệm: -{{ number_format($discount, 0, ',', '.') }}₫
                    </p>
                </div>
            </div>
            <button
                type="button"
                wire:click="removeCoupon"
                class="text-[10px] tracking-[0.12em] uppercase text-muted-text hover:text-badge-hot transition-colors ml-4 focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
            >
                Xoá
            </button>
        </div>
    @else
        {{-- Input state --}}
        <div class="flex items-center border-b border-[#1a1a1a]">
            <input
                type="text"
                wire:model="couponCode"
                placeholder="Nhập mã giảm giá"
                class="flex-1 bg-transparent py-2 text-sm outline-none uppercase placeholder:normal-case placeholder:text-muted-text font-light tracking-wider"
                aria-label="Mã giảm giá"
            >
            <button
                type="button"
                wire:click="applyCoupon"
                wire:loading.attr="disabled"
                class="shrink-0 text-[10px] tracking-[0.2em] uppercase py-2 pl-4 hover:opacity-60 transition-opacity disabled:opacity-40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]"
            >
                <span wire:loading.remove wire:target="applyCoupon">Áp Dụng</span>
                <span wire:loading wire:target="applyCoupon" class="flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Kiểm tra...
                </span>
            </button>
        </div>

        {{-- Error message --}}
        @if($errorMessage)
            <p class="mt-2 text-xs text-badge-hot flex items-center gap-1.5" role="alert">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $errorMessage }}
            </p>
        @endif
    @endif
</div>
