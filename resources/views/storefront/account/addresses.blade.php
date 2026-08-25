@extends("layouts.storefront")

@pushonce("page_title")Sổ Địa Chỉ — @endpushonce
@pushonce("meta_description")Quản lý địa chỉ giao hàng và thanh toán tại {{ config("app.name", "Sober Furniture") }}.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        
        {{-- ── Account Header ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-[#E5E5E5]">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Sổ Địa Chỉ</h1>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 {{ $customer->membership_tier_badge_classes }}">
                        {{ $customer->membership_tier }}
                    </span>
                </div>
                <p class="text-xs text-[#888888] font-light mt-1">
                    Xin chào, <span class="text-[#23232C] font-semibold">{{ $customer->name }}</span> ({{ $customer->email }})
                </p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route("account.addresses.create") }}" class="btn-dark text-xs uppercase tracking-wider">Thêm Địa Chỉ Mới</a>
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline">
                    Hồ Sơ
                </a>
                <form method="POST" action="{{ route("account.logout") }}">
                    @csrf
                    <button type="submit" class="text-xs tracking-wider uppercase text-[#888888] hover:text-[#E84444] transition-colors cursor-pointer">
                        Đăng Xuất
                    </button>
                </form>
            </div>
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

        {{-- Addresses by Type --}}
        @foreach(["shipping" => "Giao Hàng", "billing" => "Thanh Toán"] as $type => $label)
            @php $typeAddresses = $addresses->where("type", $type) @endphp
            @if($typeAddresses->count() > 0)
                <div class="mb-10">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4 pb-2 border-b border-[#E5E5E5]">Địa Chỉ {{ $label }} ({{ $typeAddresses->count() }})</h3>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($typeAddresses as $address)
                            <div class="bg-white border border-[#E5E5E5] p-5 shadow-sm hover:border-[#23232C] transition-colors relative">
                                @if($address->is_default)
                                    <div class="absolute -top-2 -right-2 bg-[#23232C] text-white text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Mặc Định</div>
                                @endif
                                
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 {{ $address->type === "shipping" ? "bg-blue-50 text-blue-700 border border-blue-200" : "bg-purple-50 text-purple-700 border border-purple-200" }} rounded">
                                                {{ $address->type === "shipping" ? "Giao Hàng" : "Thanh Toán" }}
                                            </span>
                                            @if($address->label)
                                                <span class="text-[10px] text-[#888888] font-light">{{ $address->label }}</span>
                                            @endif
                                        </div>
                                        <p class="font-medium text-[#23232C]">{{ $address->recipient_name }}</p>
                                        <p class="text-xs text-[#888888]">{{ $address->phone }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if(!$address->is_default)
                                            <form method="POST" action="{{ route("account.addresses.default", $address) }}">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="text-[10px] uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline px-2 py-1" title="Đặt làm mặc định">Mặc Định</button>
                                            </form>
                                        @endif
                                        <a href="{{ route("account.addresses.edit", $address) }}" class="text-[10px] uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline px-2 py-1">Sửa</a>
                                        <form method="POST" action="{{ route("account.addresses.destroy", $address) }}" onsubmit="return confirm("Bạn có chắc chắn muốn xóa địa chỉ này?")">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="text-[10px] uppercase tracking-wider text-[#E84444] hover:text-rose-600 font-medium link-underline px-2 py-1" title="Xóa">Xóa</button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-sm text-[#23232C] leading-relaxed">{{ $address->formatted_address }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if($addresses->count() === 0)
            <div class="text-center py-20 bg-white border border-[#E5E5E5] p-8 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#888888] mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-sm font-medium text-[#23232C] mb-2">Bạn chưa có địa chỉ nào.</p>
                <p class="text-xs text-[#888888] font-light mb-6">Thêm địa chỉ để nhanh chóng thanh toán lần sau.</p>
                <a href="{{ route("account.addresses.create") }}" class="inline-block px-6 py-2.5 bg-[#23232C] text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors">
                    Thêm Địa Chỉ Đầu Tiên
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
