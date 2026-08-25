<div class="checkout-section" id="checkout-form">
    {{-- ── Success State ──────────────────────────────────────────────── --}}
    @if($successData)
        <div style="background:#f0fdf4;border:2px solid #86efac;padding:24px;border-radius:12px;text-align:center;">
            <div style="font-size:2.5rem;margin-bottom:12px;">✅</div>
            <h3 style="color:#166534;font-weight:700;font-size:1.3rem;margin-bottom:8px;">Đặt hàng thành công!</h3>

            <div style="background:#fff;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:16px;">
                <p style="color:#6b7280;font-size:0.85rem;margin-bottom:4px;">Mã đơn hàng</p>
                <p style="font-family:monospace;font-size:1.4rem;font-weight:900;color:#111827;letter-spacing:0.1em;">#{{ $successData['order_reference'] }}</p>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
                <div style="display:inline-flex;align-items:center;gap:6px;justify-content:center;padding:6px 14px;background:#dcfce7;border-radius:20px;font-size:0.9rem;font-weight:600;color:#166534;">
                    💵 Thanh toán khi nhận hàng (COD)
                </div>
                <p style="color:#4b5563;font-size:0.9rem;">
                    🚚 Dự kiến giao: <strong>{{ $successData['estimated_delivery'] }}</strong>
                </p>
            </div>

            <p style="color:#6b7280;font-size:0.82rem;line-height:1.5;">
                Chúng tôi sẽ liên hệ xác nhận đơn hàng qua số điện thoại bạn đã cung cấp.
            </p>
        </div>

    {{-- ── Order Form ─────────────────────────────────────────────────── --}}
    @else
        <h2 style="font-size:1.5rem;font-weight:600;margin-bottom:20px;">Đặt Hàng Ngay</h2>

        @if($errorMsg)
            <div style="color:#dc2626;background:#fef2f2;border:1px solid #fecaca;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:0.9rem;">
                {{ $errorMsg }}
            </div>
        @endif

        <form wire:submit="submitOrder" style="display:flex;flex-direction:column;gap:15px;">

            {{-- Honeypot --}}
            <div style="display:none;" aria-hidden="true">
                <input type="text" wire:model="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            {{-- Combo Selection --}}
            @php $combos = $landingPage->comboRules(); @endphp
            @if(count($combos) > 0)
                <div>
                <fieldset style="border:none;padding:0;margin:0;">
                    <legend style="display:block;margin-bottom:5px;font-weight:500;">Chọn Combo Ưu Đãi</legend>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($combos as $combo)
                            <label style="display:flex;align-items:center;padding:10px;border:{{ $selectedComboId === ($combo['id'] ?? '') ? '2px solid #3b82f6' : '1px solid #d1d5db' }};border-radius:8px;cursor:pointer;background:{{ $selectedComboId === ($combo['id'] ?? '') ? '#eff6ff' : '#fff' }};">
                                <input type="radio"
                                    wire:model.live="selectedComboId"
                                    value="{{ $combo['id'] ?? '' }}"
                                    style="margin-right:10px;">
                                <div style="flex:1;">
                                    <div style="font-weight:600;">{{ $combo['name'] ?? '' }}</div>
                                </div>
                                <div style="font-weight:bold;color:#e11d48;">
                                    {{ number_format($combo['price'] ?? 0, 0, ',', '.') }}₫
                                </div>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                @error('selectedComboId') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Name --}}
            <div>
                <label for="lof-name" style="display:block;margin-bottom:5px;font-weight:500;">Họ và Tên <span style="color:#e11d48;">*</span></label>
                <input
                    type="text"
                    id="lof-name"
                    wire:model="name"
                    placeholder="Nguyễn Văn A"
                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #d1d5db;box-sizing:border-box;"
                    required>
                @error('name') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="lof-phone" style="display:block;margin-bottom:5px;font-weight:500;">Số Điện Thoại <span style="color:#e11d48;">*</span></label>
                <input
                    type="tel"
                    id="lof-phone"
                    wire:model="phone"
                    placeholder="09xxxxxxxx"
                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #d1d5db;box-sizing:border-box;"
                    required>
                @error('phone') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="lof-address" style="display:block;margin-bottom:5px;font-weight:500;">Địa chỉ nhận hàng <span style="color:#e11d48;">*</span></label>
                <textarea
                    id="lof-address"
                    wire:model="address"
                    placeholder="Số nhà, Đường, Phường, Quận, Thành phố..."
                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #d1d5db;min-height:80px;box-sizing:border-box;"
                    required></textarea>
                @error('address') <span style="color:#dc2626;font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            {{-- Note --}}
            <div>
                <label for="lof-note" style="display:block;margin-bottom:5px;font-weight:500;">Ghi chú <span style="color:#9ca3af;font-weight:400;">(Tùy chọn)</span></label>
                <input
                    type="text"
                    id="lof-note"
                    wire:model="note"
                    placeholder="Giao giờ hành chính, gọi trước khi giao..."
                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #d1d5db;box-sizing:border-box;">
            </div>

            {{-- Out of stock --}}
            @if(!$landingPage->isInStock())
                <div style="background:#fee2e2;color:#991b1b;padding:15px;border-radius:8px;text-align:center;font-weight:bold;">
                    ⚠️ Tạm Hết Hàng — Sản phẩm hiện đang tạm ngưng nhận đơn mới.
                </div>
            @else
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    style="width:100%;padding:15px;background:#ea580c;color:#fff;font-size:1.1rem;font-weight:bold;border:none;border-radius:8px;cursor:pointer;margin-top:10px;transition:background 0.2s;"
                    onmouseover="this.style.background='#c2410c'"
                    onmouseout="this.style.background='#ea580c'">
                    <span wire:loading.remove>🛒 HOÀN TẤT ĐẶT HÀNG</span>
                    <span wire:loading>⏳ Đang xử lý...</span>
                </button>
            @endif
        </form>
    @endif
</div>
