<div
    class="bg-white border border-[#E5E5E5] p-6 sticky top-24"
    x-data="{
        breakdown: @entrust($breakdown->toJson()),
        subtotal: {{ $subtotal }},
        shippingFee: {{ $shippingFee ?? 0 }},
        fmt: (n) => new Intl.NumberFormat(\"vi-VN\").format(n) + \"\u20ab\",
    }"
    :class="{ \"dark:bg-[#1a1a1a] dark:border-[#333] dark:text-white\": theme === \"dark\" }"
>

    <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-6 pb-3 border-b border-[#E5E5E5]">
        T\u1ed4m T\u1eaft \u0110\u01a1n H\u00e0ng
    </h2>

    <ul class="divide-y divide-[#E5E5E5] mb-6" aria-label="S\u1ea3n ph\u1ea9m trong \u0111\u01a1n h\u00e0ng">
        <template x-for="item in breakdown.cartItems" :key="item.product_id + \"_\" + (item.product_variant_id || 0)">
            <li class="py-4 flex items-start gap-4">
                <div class="w-16 h-16 flex-shrink-0 bg-[#F0F0F0] overflow-hidden">
                    <template x-if="item.image_path">
                        <img :src="item.image_path" :alt="item.product_name" class="w-full h-full object-cover" loading="lazy">
                    </template>
                    <template x-if="!item.image_path">
                        <div class="w-full h-full bg-[#E8E4DF]"></div>
                    </template>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm leading-snug truncate" x-text="item.product_name"></p>
                    <template x-if="item.variant_name">
                        <p class="text-xs text-[#888888] mt-0.5" x-text="item.variant_name"></p>
                    </template>
                    <p class="text-xs text-[#888888] mt-0.5" x-text="\"S\u1ed1 l\u01b0\u1ee3ng: \" + item.quantity"></p>
                </div>

                <p class="text-sm font-medium shrink-0" x-text="fmt(item.price * item.quantity)"></p>
            </li>
        </template>
    </ul>

    <template x-if="showFreeGifts && breakdown.freeGifts && breakdown.freeGifts.length > 0">
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block mb-1.5 flex items-center gap-1.5">
                <span>\uD83C\uDFF1</span> QU\u00c0 T\u1eafNG K\u1e8c \u0110\u01a0N H\u00c0NG
            </span>
            <template x-for="gift in breakdown.freeGifts" :key="gift.product_id">
                <div class="flex justify-between text-xs text-amber-900">
                    <span x-text="gift.name || gift.product_name || \"S\u1ea3n ph\u1ea9m qu\u00e0 t\u1eafng\" + (\" (\" + (gift.quantity || 1) + \"x)\")"></span>
                    <span class="font-bold text-emerald-700">Mi\u1ec5n ph\u00ed (0\u20ab)</span>
                </div>
            </template>
        </div>
    </template>

    <div class="border-t border-[#E5E5E5] pt-5 space-y-3">
        <div class="flex justify-between text-sm">
            <span class="text-[#888888]">T\u1ea1m t\u00ednh</span>
            <span x-text="fmt(breakdown.subtotal)"></span>
        </div>

        <template x-if="showShipping">
            <div class="flex justify-between text-sm">
                <span class="text-[#888888]">Ph\u00ed v\u1eadn chuy\u1ec3n</span>
                <span x-text="breakdown.finalShippingFee <= 0 ? \"Mi\u1ec5n ph\u00ed\" : fmt(breakdown.finalShippingFee)"></span>
            </div>
        </template>

        <template x-if="showPromotions && breakdown.appliedRules && breakdown.appliedRules.length > 0">
            <div class="py-2 border-t border-dashed border-[#E5E5E5] space-y-2">
                <span class="text-[10px] font-semibold text-[#888888] uppercase tracking-wider block">\u01a0u \u0111\u00e3i \u00e1p d\u1ee5ng</span>
                <div class="space-y-1.5">
                    <template x-for="rule in breakdown.appliedRules" :key="rule.ruleId">
                        <div class="flex justify-between items-center text-xs text-emerald-700 bg-emerald-50/80 px-2.5 py-1.5 rounded border border-emerald-200/60">
                            <div class="flex items-center gap-1.5 truncate max-w-[70%]">
                                <svg class="w-3.5 h-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="truncate font-medium" x-text="rule.ruleName"></span>
                                <template x-if="rule.isCoupon">
                                    <span class="text-[9px] bg-emerald-200 text-emerald-800 px-1 rounded uppercase font-bold">M\u00e3 coupon</span>
                                </template>
                            </div>
                            <span class="font-bold shrink-0" x-text="\"-\" + fmt(rule.discountAmount)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="showPromotions && breakdown.totalDiscount > 0">
            <div class="flex justify-between text-sm text-emerald-700 font-medium">
                <span>T\u1ed5ng gi\u1ea3m gi\u00e1</span>
                <span x-text="\"-\" + fmt(breakdown.totalDiscount)"></span>
            </div>
        </template>

        <div class="flex justify-between pt-4 border-t border-[#E5E5E5] items-baseline">
            <span class="text-sm font-medium tracking-wide">T\u1ed5ng thanh to\u00e1n</span>
            <span class="text-xl font-bold text-[#E84444]" x-text="fmt(breakdown.finalTotal)"></span>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-2 text-xs text-[#888888]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        Thanh to\u00e1n an to\u00e0n. Th\u00f4ng tin \u0111\u01b0\u1ee3c m\u00e3 ho\u00e1 SSL 256-bit.
    </div>
</div>

<script>
    document.addEventListener("livewire:initialized", () => {
        Livewire.on("refresh-summary", (data) => {
            window.dispatchEvent(new CustomEvent("order-summary-refresh", { detail: data }));
        });
    });
</script>
