@extends("layouts.storefront")

@pushonce("page_title"){{ isset($address) ? "Chỉnh Sửa Địa Chỉ" : "Thêm Địa Chỉ Mới" }} — @endpushonce
@pushonce("meta_description"){{ isset($address) ? "Chỉnh sửa" : "Thêm mới" }} địa chỉ {{ $address->type === "billing" ? "thanh toán" : "giao hàng" }} tại {{ config("app.name", "Sober Furniture") }}.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.addresses") }}" class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#23232C] font-medium link-underline inline-block mb-4">
                    ← Quay lại sổ địa chỉ
                </a>
                <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">{{ isset($address) ? "Chỉnh Sửa Địa Chỉ" : "Thêm Địa Chỉ Mới" }}</h1>
            </div>

            {{-- Flash Messages --}}
            @if(session("success"))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session("success") }}
                </div>
            @endif
            @if(session("error"))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
                    {{ session("error") }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ isset($address) ? route("account.addresses.update", $address) : route("account.addresses.store") }}" class="bg-white border border-[#E5E5E5] p-6 shadow-sm space-y-6">
                @csrf
                @if(isset($address))
                    @method("PUT")
                @endif

                {{-- Type --}}
                <div>
                    <label class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Loại địa chỉ <span class="text-[#E84444]">*</span></label>
                    <div class="flex gap-4">
                        @foreach(["shipping" => "Giao Hàng", "billing" => "Thanh Toán"] as $value => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="{{ $value }}" {{ old("type", $address->type ?? "shipping") === $value ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a]" {{ isset($address) ? "disabled" : "" }}>
                                <span class="text-sm text-[#23232C] font-light">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if(isset($address))
                        <input type="hidden" name="type" value="{{ $address->type }}">
                    @endif
                    @error("type")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Label --}}
                <div>
                    <label for="label" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Nhãn <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                    <input type="text" name="label" id="label" value="{{ old("label", $address->label ?? "") }}" placeholder="Ví dụ: Nhà riêng, Văn phòng, Nhà người thân" class="input-underline w-full @error("label") border-[#E84444] @enderror">
                    @error("label")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Recipient Name --}}
                <div>
                    <label for="recipient_name" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Tên người nhận <span class="text-[#E84444]">*</span></label>
                    <input type="text" name="recipient_name" id="recipient_name" value="{{ old("recipient_name", $address->recipient_name ?? "") }}" required autocomplete="name" class="input-underline w-full @error("recipient_name") border-[#E84444] @enderror">
                    @error("recipient_name")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Số điện thoại <span class="text-[#E84444]">*</span></label>
                    <input type="tel" name="phone" id="phone" value="{{ old("phone", $address->phone ?? "") }}" required autocomplete="tel" class="input-underline w-full @error("phone") border-[#E84444] @enderror">
                    @error("phone")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Address Line 1 --}}
                <div>
                    <label for="address_line_1" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Địa chỉ chi tiết <span class="text-[#E84444]">*</span></label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old("address_line_1", $address->address_line_1 ?? "") }}" required placeholder="Số nhà, tên đường, tòa nhà, 층, phòng..." class="input-underline w-full @error("address_line_1") border-[#E84444] @enderror">
                    @error("address_line_1")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Address Line 2 --}}
                <div>
                    <label for="address_line_2" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Địa chỉ bổ sung <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old("address_line_2", $address->address_line_2 ?? "") }}" placeholder="Khu dân cư, khu phố, thôn, xóm..." class="input-underline w-full @error("address_line_2") border-[#E84444] @enderror">
                    @error("address_line_2")
                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- City / Province --}}
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Tỉnh/Thành phố <span class="text-[#E84444]">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old("city", $address->city ?? "") }}" required autocomplete="address-level1" class="input-underline w-full @error("city") border-[#E84444] @enderror">
                        @error("city")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Quận/Huyện <span class="text-[#E84444]">*</span></label>
                        <input type="text" name="district" id="district" value="{{ old("district", $address->district ?? "") }}" required autocomplete="address-level2" class="input-underline w-full @error("district") border-[#E84444] @enderror">
                        @error("district")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="ward" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Phường/Xã <span class="text-[#E84444]">*</span></label>
                        <input type="text" name="ward" id="ward" value="{{ old("ward", $address->ward ?? "") }}" required autocomplete="address-level3" class="input-underline w-full @error("ward") border-[#E84444] @enderror">
                        @error("ward")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Postal Code & Country --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="postal_code" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Mã bưu chính <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old("postal_code", $address->postal_code ?? "") }}" autocomplete="postal-code" class="input-underline w-full @error("postal_code") border-[#E84444] @enderror">
                        @error("postal_code")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Quốc gia</label>
                        <input type="text" name="country" id="country" value="{{ old("country", $address->country ?? "Vietnam") }}" autocomplete="country" class="input-underline w-full @error("country") border-[#E84444] @enderror">
                        @error("country")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Default Address --}}
                <div class="flex items-center gap-3 pt-2 border-t border-[#E5E5E5]">
                    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old("is_default", $address->is_default ?? false) ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                    <label for="is_default" class="text-sm text-[#23232C] font-light cursor-pointer">Đặt làm địa chỉ {{ $address->type === "billing" ? "thanh toán" : "giao hàng" }} mặc định</label>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-[#E5E5E5]">
                    <a href="{{ route("account.addresses") }}" class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#E84444] font-medium link-underline text-center py-2">Hủy</a>
                    <button type="submit" class="btn-dark w-full sm:w-auto text-center py-2">{{ isset($address) ? "Cập Nhật" : "Thêm Địa Chỉ" }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
