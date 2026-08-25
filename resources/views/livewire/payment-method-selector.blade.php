<div class="space-y-4">
    <h4 class="text-xs font-semibold tracking-[0.15em] uppercase text-muted-text mb-2">Phương thức thanh toán</h4>

    <div class="space-y-3" role="radiogroup" aria-label="Chọn phương thức thanh toán">
        @foreach($availableMethods as $method)
            @if($this->isMethodEnabled($method))
                @php $details = $methodDetails[$method] ?? []; @endphp
                <div
                    class="relative border rounded-lg p-4 cursor-pointer transition-all
                        {{ $selectedMethod === $method
                            ? "border-badge-hot bg-rose-50 ring-2 ring-badge-hot"
                            : "border-border-subtle hover:border-primary-dark" }}"
                    wire:click="selectMethod(\"{{ $method }}\")"
                    role="radio"
                    aria-checked="{{ $selectedMethod === $method ? "true" : "false" }}"
                    tabindex="0"
                    @keydown.enter.prevent="selectMethod(\"{{ $method }}\")"
                    @keydown.space.prevent="selectMethod(\"{{ $method }}\")"
                >
                    <div class="flex items-center gap-4">
                        <input type="radio"
                            name="payment_method"
                            value="{{ $method }}"
                            {{ $selectedMethod === $method ? "checked" : "" }}
                            class="sr-only"
                            aria-hidden="true">

                        <div class="w-12 h-12 bg-surface-bg border border-border-subtle rounded-lg flex items-center justify-center shrink-0">
                            @if($method === "cod")
                                <svg class="w-6 h-6 text-muted-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($method === "vnpay")
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                </svg>
                            @elseif($method === "momo")
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M17 12c0 2.76-2.24 5-5 5s-5-2.24-5-5 2.24-5 5-5 5 2.24 5 5zm-5 2c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z" />
                                </svg>
                            @elseif($method === "banking")
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-primary-dark">{{ $details["name"] ?? ucfirst($method) }}</span>
                                @if($details["fee"] ?? 0 > 0)
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded">
                                        +{{ number_format($details["fee"], 0, ",", ".") }}đ phí
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-muted-text">{{ $details["description"] ?? "" }}</p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <svg class="w-5 h-5 text-badge-hot" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- ── Method Details ── --}}
    @if($showDetails && !empty($this->getSelectedMethodDetails()))
        <div class="mt-4 p-4 bg-[#F9F9F9] border border-border-subtle rounded-lg animate-fade-in">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold tracking-[0.15em] uppercase text-primary-dark">
                    Chi tiết: {{ $this->getSelectedMethodDetails()["name"] ?? "" }}
                </span>
                <button type="button"
                    wire:click="toggleDetails"
                    class="text-muted-text hover:text-primary-dark p-1"
                    aria-label="Đóng chi tiết">
                    <x-icons.close class="w-5 h-5" />
                </button>
            </div>

            <div class="text-sm text-primary-dark leading-relaxed">
                {{ $this->getSelectedMethodDetails()["description"] ?? "" }}

                @if($selectedMethod === "vnpay")
                    <div class="mt-3 space-y-1 text-xs text-muted-text">
                        <p>• Quét mã QR hoặc nhập thông tin thẻ/VNPAY</p>
                        <p>• Hỗ trợ ATM, Internet Banking, ví điện tử</p>
                        <p>• Thanh toán ngay lập tức, không cần tiền mặt</p>
                    </div>
                @elseif($selectedMethod === "momo")
                    <div class="mt-3 space-y-1 text-xs text-muted-text">
                        <p>• Mở ứng dụng MoMo để xác nhận thanh toán</p>
                        <p>• Hỗ trợ thanh toán qua MoMo App hoặc MoMo Web</p>
                        <p>• An toàn, nhanh chóng, không mất phí</p>
                    </div>
                @elseif($selectedMethod === "banking")
                    <div class="mt-3 space-y-1 text-xs text-muted-text">
                        <p>• Chuyển khoản qua ứng dụng ngân hàng</p>
                        <p>• Nội dung chuyển khoản: Mã đơn hàng</p>
                        <p>• Đơn hàng được xử lý sau khi nhận được tiền</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="text-center pt-2">
        <button type="button"
            wire:click="toggleDetails"
            class="inline-flex items-center gap-1 text-xs text-muted-text hover:text-badge-hot font-medium link-underline">
            @if($showDetails)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                Thu gọn chi tiết
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                Xem chi tiết phương thức
            @endif
        </button>
    </div>
</div>
