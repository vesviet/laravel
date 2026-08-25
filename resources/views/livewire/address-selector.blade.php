<div class="space-y-6">
    {{-- ── Saved Addresses ── --}}
    @if($addresses->count() > 0)
        <div class="mb-6">
            <h4 class="text-xs font-semibold tracking-[0.15em] uppercase text-[#888888] mb-3">
                Địa chỉ {{ $addressType === "shipping" ? "giao hàng" : "thanh toán" }} đã lưu
            </h4>
            <div class="space-y-3" role="radiogroup" aria-label="Chọn địa chỉ">
                @foreach($addresses as $address)
                    <div
                        class="relative border rounded-lg p-4 cursor-pointer transition-all
                            {{ !empty($selectedAddress) && $selectedAddress["id"] == $address["id"]
                                ? "border-[#E84444] bg-rose-50 ring-2 ring-[#E84444]"
                                : "border-[#E5E5E5] hover:border-[#23232C]" }}"
                        wire:click="selectAddress({{ json_encode($address) }})"
                        role="radio"
                        aria-checked="{{ !empty($selectedAddress) && $selectedAddress["id"] == $address["id"] ? "true" : "false" }}"
                        tabindex="0"
                        @keydown.enter.prevent="selectAddress({{ json_encode($address) }})"
                        @keydown.space.prevent="selectAddress({{ json_encode($address) }})"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <input type="radio"
                                    name="address_selector"
                                    value="{{ $address["id"] }}"
                                    {{ !empty($selectedAddress) && $selectedAddress["id"] == $address["id"] ? "checked" : "" }}
                                    class="sr-only"
                                    aria-hidden="true">
                                <div class="w-10 h-10 bg-[#F0F0F0] border border-[#E5E5E5] rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-medium text-[#23232C]">{{ $address["recipient_name"] }}</span>
                                        @if($address["label"])
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded">
                                                {{ $address["label"] }}
                                            </span>
                                        @endif
                                        @if($address["is_default"])
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">
                                                Mặc định
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#888888]">{{ $address["phone"] }}</p>
                                    <p class="text-xs text-[#888888] mt-0.5 line-clamp-1">{{ $address["formatted_address"] }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0">
                                @if(!$address["is_default"])
                                    <button type="button"
                                        wire:click.stop="setDefault({{ $address["id"] }})"
                                        class="text-[10px] uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline px-2 py-1"
                                        aria-label="Đặt làm mặc định">
                                        Mặc định
                                    </button>
                                @endif
                                <button type="button"
                                    wire:click.stop="deleteAddress({{ $address["id"] }})"
                                    class="text-[10px] uppercase tracking-wider text-[#E84444] hover:text-rose-600 font-medium link-underline px-2 py-1"
                                    aria-label="Xóa địa chỉ"
                                    onclick="return confirm(\"Bạn có chắc chắn muốn xóa địa chỉ này?\")">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-8 bg-white border border-[#E5E5E5] rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-[#888888] mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-sm font-medium text-[#23232C] mb-1">Chưa có địa chỉ {{ $addressType === "shipping" ? "giao hàng" : "thanh toán" }} nào</p>
            <p class="text-xs text-[#888888] font-light mb-4">Nhấn nút bên dưới để thêm địa chỉ đầu tiên</p>
            <button type="button"
                wire:click="toggleNewAddressForm"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#23232C] text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm địa chỉ mới
            </button>
        </div>
    @endif

    {{-- ── Add New Address Button ── --}}
    @if($addresses->count() > 0)
        <div class="text-center">
            <button type="button"
                wire:click="toggleNewAddressForm"
                class="inline-flex items-center gap-2 px-4 py-2 border border-[#E5E5E5] text-[#23232C] hover:bg-[#F5F5F5] text-xs font-semibold uppercase tracking-wider transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm địa chỉ mới
            </button>
        </div>
    @endif

    {{-- ── New Address Form ── --}}
    @if($showNewAddressForm)
        <div class="bg-white border border-[#E5E5E5] p-6 rounded-lg mt-6 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-semibold tracking-[0.15em] uppercase text-[#23232C]">
                    Thêm địa chỉ {{ $addressType === "shipping" ? "giao hàng" : "thanh toán" }} mới
                </h4>
                <button type="button"
                    wire:click="toggleNewAddressForm"
                    class="text-[#888888] hover:text-[#23232C] p-1"
                    aria-label="Đóng form">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="saveNewAddress" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="label" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Nhãn <span class="text-[#888888] text-[9px] normal-case">(tuỳ chọn)</span></label>
                        <input type="text"
                            wire:model="newAddress.label"
                            id="label"
                            placeholder="Ví dụ: Nhà riêng, Văn phòng..."
                            class="input-underline w-full @error("newAddress.label") border-[#E84444] @enderror">
                        @error("newAddress.label")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="type" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Loại địa chỉ</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio"
                                    wire:model="newAddress.type"
                                    value="shipping"
                                    class="h-4 w-4 accent-[#1a1a1a]">
                                <span class="text-sm text-[#23232C] font-light">Giao hàng</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio"
                                    wire:model="newAddress.type"
                                    value="billing"
                                    class="h-4 w-4 accent-[#1a1a1a]">
                                <span class="text-sm text-[#23232C] font-light">Thanh toán</span>
                            </label>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="recipient_name" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Tên người nhận <span class="text-[#E84444]">*</span></label>
                        <input type="text"
                            wire:model="newAddress.recipient_name"
                            id="recipient_name"
                            required
                            class="input-underline w-full @error("newAddress.recipient_name") border-[#E84444] @enderror">
                        @error("newAddress.recipient_name")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Số điện thoại <span class="text-[#E84444]">*</span></label>
                        <input type="tel"
                            wire:model="newAddress.phone"
                            id="phone"
                            required
                            class="input-underline w-full @error("newAddress.phone") border-[#E84444] @enderror">
                        @error("newAddress.phone")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address_line_1" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Địa chỉ chi tiết <span class="text-[#E84444]">*</span></label>
                        <input type="text"
                            wire:model="newAddress.address_line_1"
                            id="address_line_1"
                            required
                            placeholder="Số nhà, tên đường, tòa nhà, tầng, phòng..."
                            class="input-underline w-full @error("newAddress.address_line_1") border-[#E84444] @enderror">
                        @error("newAddress.address_line_1")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address_line_2" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Địa chỉ bổ sung <span class="text-[#888888] text-[9px] normal-case">(tuỳ chọn)</span></label>
                        <input type="text"
                            wire:model="newAddress.address_line_2"
                            id="address_line_2"
                            placeholder="Khu dân cư, khu phố, thôn, xóm..."
                            class="input-underline w-full @error("newAddress.address_line_2") border-[#E84444] @enderror">
                        @error("newAddress.address_line_2")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Tỉnh/Thành phố <span class="text-[#E84444]">*</span></label>
                        <input type="text"
                            wire:model="newAddress.city"
                            id="city"
                            required
                            class="input-underline w-full @error("newAddress.city") border-[#E84444] @enderror">
                        @error("newAddress.city")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Quận/Huyện <span class="text-[#E84444]">*</span></label>
                        <input type="text"
                            wire:model="newAddress.district"
                            id="district"
                            required
                            class="input-underline w-full @error("newAddress.district") border-[#E84444] @enderror">
                        @error("newAddress.district")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="ward" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Phường/Xã <span class="text-[#E84444]">*</span></label>
                        <input type="text"
                            wire:model="newAddress.ward"
                            id="ward"
                            required
                            class="input-underline w-full @error("newAddress.ward") border-[#E84444] @enderror">
                        @error("newAddress.ward")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Mã bưu chính <span class="text-[#888888] text-[9px] normal-case">(tuỳ chọn)</span></label>
                        <input type="text"
                            wire:model="newAddress.postal_code"
                            id="postal_code"
                            class="input-underline w-full @error("newAddress.postal_code") border-[#E84444] @enderror">
                        @error("newAddress.postal_code")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Quốc gia</label>
                        <input type="text"
                            wire:model="newAddress.country"
                            id="country"
                            class="input-underline w-full @error("newAddress.country") border-[#E84444] @enderror">
                        @error("newAddress.country")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-[#E5E5E5]">
                    <input type="checkbox"
                        wire:model="newAddress.is_default"
                        id="is_default"
                        class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                    <label for="is_default" class="text-sm text-[#23232C] font-light cursor-pointer">
                        Đặt làm địa chỉ {{ $addressType === "billing" ? "thanh toán" : "giao hàng" }} mặc định
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[#E5E5E5]">
                    <button type="button"
                        wire:click="toggleNewAddressForm"
                        class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#E84444] font-medium link-underline text-center py-2 flex-1">
                        Hủy
                    </button>
                    <button type="submit"
                        class="btn-dark w-full sm:w-auto text-center py-2 flex-1">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
</style>
